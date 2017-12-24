<?php
require_once __DIR__.'/appIndex.php';


//创建一个tcp套接字,并监听8088端口
if($web = stream_socket_server('0.0.0.0:80',$errno,$errstr)){
    while(true){
        $conn = @stream_socket_accept($web);
        if($conn){
            $contentLen = 0;
            $_GET = [];
            $_POST = [];
            $request = fgets($conn);
            $postMatch = preg_match('/POST\s\/\?([\S]*)\s/', $request, $post_arr);           //获取post的路径传过的参数
            $getMatch = preg_match('/GET\s\/\?([\S]*)\s/', $request, $get_arr);           //获取get的路径参数
            if ($postMatch) {
                $paramArr = explode('&', $post_arr[1]);
                foreach ($paramArr as $value) {
                    $spilt = explode('=', $value);
                    $_GET[$spilt[0]] = $spilt[1];
                }
            }
            if ($getMatch) {
                $paramArr = explode('&', $get_arr[1]);
                foreach ($paramArr as $value) {
                    $spilt = explode('=', $value);
                    $_GET[$spilt[0]] = $spilt[1];
                }
            }

            while ($line = fgets($conn)) {
                if ($line == "\r\n")
                    break;
                $res = preg_match('/Content-Length:\s([\S]*)/', $line, $arr);
                if ($res) {
                    $contentLen = $arr[1];
                }
//                print_r($line);
            }
            $content = '';
            for ($i = 1; $i <= $contentLen; $i++) {
                $str = fgetc($conn);
                $content .= $str;
            }
            if($content)
            {
                $_POST['xml'] = $content;
            }
            AppIndex::setConn($conn);
            AppIndex::index();
            fclose($conn);
        }
    }
}else{
    die($errstr);
}
