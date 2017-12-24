<?php
class Logic
{
    public function setSchedule($num,$pwd,$userOpenId)
    {
        $db = Db::getDb();
        $res = $db->query("select id from users where openId='$userOpenId' and login=1")->fetchAll(PDO::FETCH_ASSOC);
        if(!$res)
        {
            $url = 'https://smallsi.com:9503/?number='.$num.'&password='.$pwd.'&type=schedule';
            $res = json_decode(Tools::get($url),true);
            if(!$res['error'])
            {

                $schedule = json_encode($res['content']);
                $schedule = addslashes ($schedule);
                $row = $db->exec("update users set schedule='$schedule',login=1 where openId='$userOpenId'");
                if($row)
                {
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return true;
        }

    }
    static function setUser($xmlObj)
    {
        $db = Db::getDb();
        $userOpenId = $xmlObj->FromUserName;
        $sql = "select id from users where openId='$userOpenId'";
        $res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if(!$res)
        {
            $sql = "insert into users(openId,subscribe) values('$userOpenId',1)";
            $res = $db->exec($sql);
        }
        if($res)
        {
            Tools::sendTemplateMsg('welcome',$userOpenId.'');
        }
    }
    static function setDayRemindOpen($openId)
    {
        $db = Db::getDb();
        $setRes = $db->exec("update users set dayRemind=1 where openId='$openId' and dayRemind=0");
        if($setRes)
        {
            return true;
        }else{
            return false;
        }
    }
    static function setBeforeRemindOpen($openId)
    {
        $db = Db::getDb();
        $setRes = $db->exec("update users set beforeRemind=1 where openId='$openId' and beforeRemind=0");
        if($setRes)
        {
            return true;
        }else{
            return false;
        }
    }
    static function setDayRemindClose($openId)
    {
        $db = Db::getDb();
        $setRes = $db->exec("update users set dayRemind=0 where openId='$openId' and dayRemind=1");
        if($setRes)
        {
            return true;
        }else{
            return false;
        }
    }
    static function setBeforeRemindClose($openId)
    {
        $db = Db::getDb();
        $setRes = $db->exec("update users set beforeRemind=0 where openId='$openId' and beforeRemind=1");
        if($setRes)
        {
            return true;
        }else{
            return false;
        }
    }
    static function getRemindStatus($openId)
    {
        $db = Db::getDb();
        $arr = $db->query("select beforeRemind,dayRemind from users where openId='$openId'")->fetchAll(PDO::FETCH_ASSOC);
        if($arr[0]['beforeRemind'])
        {
            $beforeRemind = '开启';
        }else{
            $beforeRemind = '关闭';
        }
        if($arr[0]['dayRemind'])
        {
            $dayRemind = '开启';
        }else{
            $dayRemind = '关闭';
        }
        return '当日提醒：'.$dayRemind.'  课前提醒：'.$beforeRemind;
    }
    static function getThisWeekSch($week)
    {
        $userOpenId = Tools::$userOpenId;
        $db = Db::getDb();
        $res = $db->query("select schedule from users where openId='$userOpenId'")->fetchAll(PDO::FETCH_ASSOC);
        $scheduleArr = json_decode($res[0]['schedule'],true);
        return $scheduleArr[$week];
    }
    static function setUnSubscribe(){
        $userOpenId = Tools::$userOpenId;
        $db = Db::getDb();
        $setRes = $db->exec("update users set subscribe=0 where openId='$userOpenId'");
    }

}