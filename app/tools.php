<?php
/**
 * Class Tools 工具类
 */
class Tools{
    static private $appId = 'wx730b7f9a60839346';
    static private $appSecret = 'ecd6e74ad59d995a23ebe19cc5dc814f';
    static private $accessToken = null; //开发者accessToken
    static private $templateMsgData = [         //模板
      'welcome'=>'j3nXKTJJzf_Xt-5Jutusvr8g4ICAs6L2BPeyYmdB_ro',
        'schedule'=>'EhDA6g21aorgKJiLCizej2utNhgEkpfdxtka-CPY890'
    ];
    static public $userOpenId = null;
    static public $myId = null;
    /**
     * @param $content  //写进文件的主题
     * @param string $head 写进文件那一部分的头部
     * @description 用户调试，将需要输出的变量写进文件里
     */
    static function writeIntoFile($content,$head = '')
    {
        $fileName = 'log.txt';
        $f = fopen($fileName,'a+') or die('写入文件出现错误');
        $time = date('Y-m-d H:i:s',time());
        $content = print_r($content,true);
        $content = "\n\r------------------------------------- $head  $time --------------------------------------------------------\n\r".$content;
        $content = $content."\n\r------------------------------------------------------------------------------------------------\n\r";
        fwrite($f,$content);
    }
    //将数组转化为xml字符串
    static function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }
    //将xml字符串转化为对象
    static function xmlToObj($xmlStr)
    {
        return simplexml_load_string($xmlStr, 'SimpleXMLElement', LIBXML_NOCDATA);
    }
    static private function post($url,$data=null)
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
    static public function get($url)
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
    //获取accessToken并设置
    static private function setAccessToken(){
        if(self::$accessToken)
        {
            return;
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.self::$appId.'&secret='.self::$appSecret;
        $res = self::post($url);
        $arr = json_decode($res,true);
        if(isset($arr['access_token']))
        {
            self::$accessToken = $arr['access_token'];
        }else{
            self::writeIntoFile($res,'error.txt');
        }
    }
    static function sendTextToUser($content)
    {
        AppIndex::write(self::arrayToXml([
            'ToUserName'=>self::$userOpenId,
            'FromUserName'=>self::$myId,
            'CreateTime'=>time(),
            'MsgType'=>'text',
            'Content'=>$content
        ]));
    }
    //发送欢迎界面
    static function sendTemplateMsg($templateIdKey,$userOpenId)
    {
        self::setAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.self::$accessToken;
        $data = json_encode([
            'touser'=>$userOpenId,
            'template_id'=>self::$templateMsgData[$templateIdKey]
        ],JSON_UNESCAPED_UNICODE);
        $res = json_decode(self::post($url,$data),true);
        if($res['errcode'])             //出现错误的时候，记录错误册信息
        {
            self::writeIntoFile([
                'type'=>'发送欢迎信息失败',
                'content'=>$res
            ]);
        }
    }

    static function sendSchedule($data,$openId)
    {
        self::setAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.self::$accessToken;
        $data = json_encode([
            'touser'=>$openId,
            'template_id'=>self::$templateMsgData['schedule'],
            'data'=>$data
        ],JSON_UNESCAPED_UNICODE);
        $res = json_decode(self::post($url,$data),true);
        if($res['errcode'])             //出现错误的时候，记录错误册信息
        {
            self::writeIntoFile([
                'type'=>'发送课表信息失败',
                'content'=>$res
            ]);
        }
    }
}

