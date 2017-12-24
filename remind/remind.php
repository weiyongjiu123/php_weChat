<?php
class Remind
{
    private $dsn = "mysql:dbname=weChatPublic;host=localhost";
    private $db_user = 'root';
    private $db_pass = '';
    private $pdo = null;
    private $which = null;
    private $appId = 'wx730b7f9a60839346';
    private $appSecret = 'ecd6e74ad59d995a23ebe19cc5dc814f';
    private $accessToken = null;
    private $template = [
        'remind_head'=>'hfBzFLEcAzqQ9CWncemjsXyEn0nU0fY6_nt-HKzkntk',
        'remind_item'=>'zjJSh3BYl_H8TsfJ22v0Du8jwyaN186I98F301aX2uU',
        'remind_before'=>'-JoLnDsCL9gw-Cx1jxEU9RcojkyviZQ-0qtZivyzjnk'
    ];
    private $timeArr = [
        '09：00~10：20',
        '10：40~12:00',
        '12：30~13：50',
        '14：00~15：20',
        '15：30~16：50',
        '17：00~18：20',
        '19：00~20：20',
        '20：00~21：50'
    ];
    function __construct()
    {
        try {
            $this->pdo = new PDO($this->dsn, $this->db_user, $this->db_pass);
            $this->pdo->exec("set names utf8");
        } catch (Exception $e) {
            echo '数据库连接失败' . $e->getMessage();
        }
        $this->setAccessToken();
    }
    public function beforeRemind($which)
    {
        $this->which = $which;
        $userMsg = $this->pdo->query("select openId,todaySchedule from users where subscribe=1 and login=1 and beforeRemind=1")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($userMsg as $value)
        {
//            $this->doSchedule($id['id']);
            $schedule = json_decode($value['todaySchedule'],true);
            if($schedule)
            {
                $this->sendBeforeRemindToUser($schedule,$value['openId']);
            }
        }
    }
    private function sendBeforeRemindToUser($schedule,$openId)
    {
        if(isset($schedule[$this->which]))
        {
            $this->sendBeforeRemind($schedule[$this->which],$openId);
        }
    }
    //当日提醒
    public function dayRemind()
    {
        $userMsgArr = $this->pdo->query("select openId,todaySchedule from users where subscribe=1 and login=1 and dayRemind=1")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($userMsgArr as $value)
        {
            $schedule = json_decode($value['todaySchedule'],true);
            if($schedule)
            {
                $this->sendDayRemindToUser($schedule,$value['openId']);
            }
        }
    }
    //处理课表
    private function sendBeforeRemind($arr,$openId)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$this->accessToken;
        $data = json_encode([
            'touser'=>$openId,
            'template_id'=>$this->template['remind_before'],
            'data'=>[
                'courses'=>[
                    'value'=>$arr['subject']
                ],
                'time'=>[
                    'value'=>$this->timeArr[$this->which - 1]
                ],
                'classroom'=>[
                    'value'=>$arr['classroom']
                ]
            ]
        ],JSON_UNESCAPED_UNICODE);
        $this->post($url,$data);
    }
    public function post($url,$data=null)
    {
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url);
        curl_setopt ( $ch, CURLOPT_POST, 1 );//请求方式为post
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );//不打印header信息
        if ($data) {
            curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );//post传输的数据。
        }
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );//返回结果转成字符串
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $return = curl_exec ( $ch );
        curl_close ( $ch );
        return $return;
    }
    private function get($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HEADER,0);  //表示不返回header信息
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $return = curl_exec($ch);
        curl_close($ch);
        return $return;
    }
    private function setAccessToken(){
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appId.'&secret='.$this->appSecret;
        $res = $this->post($url);
        $arr = json_decode($res,true);
        if(isset($arr['access_token']))
        {
            $this->accessToken = $arr['access_token'];
        }else{
            die($res);
        }
    }
    private function sendDayRemindToUser($schedule,$openId)
    {
        $count = count($schedule);
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$this->accessToken;
        $headData = json_encode([
            'touser'=>$openId,
            'template_id'=>$this->template['remind_head'],
            'data'=>[
                'count'=>[
                    'value'=>$count
                ]
            ]
        ],JSON_UNESCAPED_UNICODE);
        $res = $this->post($url,$headData);
        foreach ($schedule as $key => $value)
        {
            $itemData = json_encode([
                'touser'=>$openId,
                'template_id'=>$this->template['remind_item'],
                'data'=>[
                    'courses'=>[
                        'value'=>$value['subject']
                    ],
                    'time'=>[
                        'value'=>$this->timeArr[$key-1]
                    ],
                    'classroom'=>[
                        'value'=>$value['classroom']
                    ]
                ]
            ],JSON_UNESCAPED_UNICODE);
            $this->post($url,$itemData);
        }
    }
    //测试函数
    private function dd($var)
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }
}
$remind = new Remind();
if($argv[1] == 'dayRemind')
{
    $remind->dayRemind();
}else if($argv[1] == 'beforeRemind')
{
    $remind->beforeRemind($argv[2]);
}

