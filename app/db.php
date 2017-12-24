<?php
class Db
{
    static private $username = 'root';
    static private $password = '';
    static private $dsn = 'mysql:dbname=weChatPublic;host=localhost';
    static private $pdo=null;
    static public function getDb()
    {
        if(!self::$pdo)
        {
            try {
                self::$pdo = new PDO(self::$dsn,self::$username,self::$password);
                self::$pdo->exec("set names utf8");
            } catch (Exception $e) {
                echo '数据库连接失败'.$e->getMessage();
            }
        }
        return self::$pdo;
    }
}