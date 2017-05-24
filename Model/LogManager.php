<?php
namespace Model;
class LogManager
{
    private $DBManager;
    
    private static $instance = null;
    public static function getInstance()
    {
        if (self::$instance === null)
            self::$instance = new LogManager();
        return self::$instance;
    }
    
    private function __construct()
    {
        $this->DBManager = DBManager::getInstance();
    }
    
    /*function which writes into access.log all users writing actions */
    function log_access($username,$message){
        $result=date("r")." : ".$username." - ".$message."\n";
        file_put_contents("./logs/access.log",$result,FILE_APPEND|LOCK_EX);
    }
    /*function which writes into access.log all users illegal actions */
    function log_security($username,$message){
        $result=date("r")." : ".$username." - ".$message."\n";
        file_put_contents("./logs/security.log",$result,FILE_APPEND|LOCK_EX);
    }
    /*function which returns 1 if the string contains special characters like <,>,= and ; */
    function test_special_char($word){
    return (preg_match('/[\'^£$%&*()}{#~?><>,|=+¬]/',$word)); 
    } 
    
    /*function which returns true if the url begins by ./uploads/"username"/ */
     function test_path($url,$username){ 
         $strUrl="./uploads/".$username."/" ; 
         $urlLen=strlen($strUrl); 
         if(strncmp ($strUrl,$url, $urlLen )==0) { 
             return true;
            } else{ return false; } } 
            
}