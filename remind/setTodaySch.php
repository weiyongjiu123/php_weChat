<?php

class Schedule
{
    private $dsn = "mysql:dbname=weChatPublic;host=localhost";
    private $db_user = 'root';
    private $db_pass = '';
    private $pdo = null;
    private $init = [];
    private $confPath = __DIR__.'/conf.ini';
    function __construct()
    {
        try {
            $this->pdo = new PDO($this->dsn, $this->db_user, $this->db_pass);
        } catch (Exception $e) {
            echo '数据库连接失败' . $e->getMessage();
        }
        $this->init = parse_ini_file($this->confPath);
        $this->updateIniFile();

    }
    private function updateIniFile()
    {
        if($this->init['week'] > 17)
        {
            return;
        }
        if($this->init['day'] >= 7)
        {
            $this->writeIntoIni([
                'week'=>$this->init['week'] + 1,
                'day'=>1
            ]);
        }else{
            $this->writeIntoIni([
                'week'=>$this->init['week'],
                'day'=>$this->init['day'] + 1
            ]);
        }
    }

    function run()
    {
        $idArr = $this->pdo->query("select id from users where subscribe=1 and login=1")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($idArr as $value) {
            $arr = $this->getTodaySchedule($value['id']);
            $this->saveTodaySchedule($arr,$value['id']);
        }
    }
    //保存今天的课表
    private function saveTodaySchedule($arr,$id)
    {
        if($arr)
        {
            $jsonStr = json_encode($arr);
            $jsonStr = addslashes($jsonStr);
        }else{
            $jsonStr = '';
        }
        $setRes = $this->pdo->exec("update users set todaySchedule='$jsonStr' where id=$id");
        if(!$setRes)
        {
            $this->writeIntoFile('更新今天课表失败','error.txt');
        }
    }
    //获取今天的课表
    private function getTodaySchedule($id)
    {
        $schedule = $this->pdo->query("select schedule from users where id=$id")->fetchAll(PDO::FETCH_ASSOC);
        $scheduleArr = json_decode($schedule[0]['schedule'],true);
        $todayScheduleArr = $scheduleArr[$this->init['week']][$this->init['day']];
        $arr = [];
        foreach ($todayScheduleArr as $key => $value)
        {
            if($value['classroom']&&!preg_match('/^ZX[\S\s]*$/',$value['classroom']))
            {
                $arr[$key] = [
                    'subject'=>$value['subject'],
                    'classroom'=>$value['classroom']
                ];
            }
        }
        return $arr;
    }
    //将数组写入conf.ini文件里
    private function writeIntoIni($arr)
    {
        $str ='';
        foreach ($arr as $key => $value)
        {
            $str .= $key.'='.$value."\n";
        }
        $f = fopen($this->confPath, "w");
        fwrite($f,$str);
        fclose($f);
    }
    //测试函数
    function dd($var)
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }
    static function writeIntoFile($content,$fileName = 'log.txt')
    {
        $f = fopen($fileName,'a+') or die('写入文件出现错误');
        $time = date('Y-m-d H:i:s',time());
        $content = print_r($content,true);
        $content = "\n\r-------------------------------------   $time --------------------------------------------------------\n\r".$content;
        $content = $content."\n\r------------------------------------------------------------------------------------------------\n\r";
        fwrite($f,$content);
        fclose($f);
    }
}

$cro = new Schedule();
$cro->run();
