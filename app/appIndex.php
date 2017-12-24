<?php
require_once __DIR__.'/db.php';
require_once __DIR__.'/tools.php';
require_once __DIR__.'/logic.php';
require_once __DIR__.'/menu.php';
require_once __DIR__.'/text.php';
require_once __DIR__.'/content.php';

class AppIndex{
    private static $conn;
    static public function index(){
        $content = new Content();
        $content->run();
//        print_r($_POST);
//        print_r($_GET);
//        self::write('哈哈哈哈2354');
    }
    static public function setConn($conn){
        self::$conn = $conn;
    }
    static public function write($content){
        fwrite(self::$conn,self::setResponse($content));
    }
    static public function setResponse($str){
        $content = "HTTP/1.1 200 OK\r\nServer: nginx/1.11.5\r\nContent-Type: text/html;charset=utf-8\r\nContent-Length: " . strlen($str) . "\r\n\r\n{$str}";
        return $content;
    }

}