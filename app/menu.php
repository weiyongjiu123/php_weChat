<?php
class Menu
{
    public function setDayRemindOpen($xmlObj)
    {
        $setRes = Logic::setDayRemindOpen($xmlObj->FromUserName.'');
        if($setRes)
        {
            Tools::sendTextToUser('“当日提醒”已开启');
        }else{
           Tools::sendTextToUser('您已经开启了“当日提醒”，请勿重复开启');
        }
    }
    public function setBeforeRemindOpen($xmlObj)
    {
        $setRes = Logic::setBeforeRemindOpen($xmlObj->FromUserName.'');
        if($setRes)
        {
            Tools::sendTextToUser('“课前提醒”已开启');
        }else{
            Tools::sendTextToUser('您已经开启了“课前提醒”，请勿重复开启');
        }
    }
    public function setDayRemindClose($xmlObj)
    {
        $setRes = Logic::setDayRemindClose($xmlObj->FromUserName.'');
        if($setRes)
        {
            Tools::sendTextToUser('“课前提醒”已关闭');
        }else{
            Tools::sendTextToUser('您已经关闭了“课前提醒”，请勿重复关闭');
        }
    }
    public function setBeforeRemindClose($xmlObj)
    {
        $setRes = Logic::setBeforeRemindClose($xmlObj->FromUserName.'');
        if($setRes)
        {
            Tools::sendTextToUser('“课前提醒”已关闭');
        }else{
            Tools::sendTextToUser('您已经关闭了“课前提醒”，请勿重复关闭');
        }
    }
    public function getSchedule($xmlObj)
    {
       Tools::sendTextToUser('发送“s:第几周”，如“s:13”，就可以获取第13周的课表。');
    }
    public function getRemindStatus($xmlObj)
    {
        $res = Logic::getRemindStatus($xmlObj->FromUserName.'');
        Tools::sendTextToUser($res);
    }
}