<?php

class Content
{
    public function run()
    {
        if (!isset($_GET['echostr'])) {
            self::replyMsg(Tools::xmlToObj($_POST['xml']));
//            Tools::writeIntoFile($postStr);
        }else{
            AppIndex::write($_GET['echostr']);
        }
//        Tools::writeIntoFile($postStr);

    }

    private function replyMsg($xmlObj)
    {
//        Tools::writeIntoFile($xmlObj);
        if(!isset($xmlObj->FromUserName))
        {
            return;
        }
        Tools::$userOpenId = $xmlObj->FromUserName;
        Tools::$myId = $xmlObj->ToUserName;
        $type = $xmlObj->MsgType;
        $type = 'replyMsg' . ucfirst($type);
        self::$type($xmlObj);

    }

    private function replyMsgText($xmlObj)
    {
        if (!Text::index($xmlObj)) {
            Tools::sendTextToUser('你发送的是“' . $xmlObj->MsgType . '”类型消息，内容是' . $xmlObj->Content);
        }else{
            AppIndex::write('success');
        }
    }

    private function replyMsgImage($xmlObj)
    {
        Tools::sendTextToUser('你发送的是“' . $xmlObj->MsgType . '”类型消息' .
            '图片链接为：' . $xmlObj->PicUrl . '，图片消息媒体id为：' . $xmlObj->MediaId);
    }

    private function replyMsgVoice($xmlObj)
    {
        Tools::sendTextToUser('你发送的是“' . $xmlObj->MsgType . '”类型消息，' .
            '语音消息媒体id：' . $xmlObj->MediaId . '，语音格式：' . $xmlObj->Format);
    }

    private function replyMsgVideo($xmlObj)
    {
        Tools::sendTextToUser('你发送的是“' . $xmlObj->MsgType . '”类型消息，' .
            '视频消息媒体id为：' . $xmlObj->MediaId . '，视频消息缩略图的媒体id：' . $xmlObj->ThumbMediaId);
    }

    private function replyMsgShortvideo($xmlObj)
    {
        Tools::sendTextToUser('你发送的是“' . $xmlObj->MsgType . '”类型消息，' .
            '视频消息媒体id为：' . $xmlObj->MediaId . '，视频消息缩略图的媒体id：' . $xmlObj->ThumbMediaId);
    }

    private function replyMsgLocation($xmlObj)
    {
        Tools::sendTextToUser('你发送的是“' . $xmlObj->MsgType . '”类型消息，' .
            '你地理位置维度是：' . $xmlObj->Location_X . '，地理位置经度是：' . $xmlObj->Location_Y .
            '，地图缩放大小：' . $xmlObj->Scale . '，地理位置信息：' . $xmlObj->Label);
    }

    private function replyMsgLink($xmlObj)
    {
        Tools::sendTextToUser('你发送的是“' . $xmlObj->MsgType . '”类型消息，' .
            '消息标题：' . $xmlObj->Title . '，消息描述：' . $xmlObj->Description . '，消息链接：' . $xmlObj->Url);
    }

    private function replyMsgEvent($xmlObj)
    {
        switch ($xmlObj->Event) {
            case 'subscribe':
                Logic::setUser($xmlObj);
                AppIndex::write('success');
                break;
            case 'CLICK': {
                $method = $xmlObj->EventKey . '';
                $menu = new Menu();
                $menu->$method($xmlObj);
                break;
            }
            case 'unsubscribe':
                Logic::setUnSubscribe();
                break;
            default:
                Tools::sendTextToUser('你发送的是“' . $xmlObj->MsgType . '”类型消息，' .
                    'event事件类型是：' . $xmlObj->Event . '，key值是' . $xmlObj->EventKey);
        }

    }

}