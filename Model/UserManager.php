<?php
namespace Model;
error_reporting(~E_DEPRECATED);
class UserManager
{
    private $DBManager;
    
    private static $instance = null;
    public static function getInstance()
    {
        if (self::$instance === null)
            self::$instance = new UserManager();
        return self::$instance;
    }
    
    private function __construct()
    {
        $this->DBManager = DBManager::getInstance();
    }
    
    //function which returns the data of username from user
    function get_user_by_username($username){
        $data = $this->DBManager->findOneSecure("select * from `user` where `username`= :username",
        ['username' => $username]);
        return $data;
    }
    
    //function which returns true if the mail already exists in database, else returns false
    function is_mail_already_exist($mail){
        $data = $this->DBManager->findAllSecure("select * from `user` where `email`= :mail",
        ['mail' => $mail]);
        return (count($data) != 0);
    }
    
    //Function which takes $data(eventually $_POST) and returns true if data entries correspond in the database
    function user_check_register($data){
        if (empty($data['username']) OR empty($data['firstname']) OR empty($data['lastname']) OR empty($data['passw']) OR empty($data['mail']))
            return 1;
        if(count($this->get_user_by_username($data['username']))!=0){
            return 2;
        }
        if($this->is_mail_already_exist($data['mail'])){
            return 3;
        }
        return 0;
    }
    
    //Function which takes $data(eventually $_POST) and returns true if username and password corresponds in the database
    function user_check_login($data){
        if (empty($data['username']) OR empty($data['passw']))
            return false;
        $result = $this->DBManager->findAllSecure("select * from `user` where `username`= :username and `password`= :password",
        ['username'=> $data['username'],'password'=> $data['passw']]);
        if(count($result)!=1){
            return false;
        }
        return true;
    }
    
    
    //FUNCTIONS CALLED BY CONTROLLERS
    
    /*function called by controller register_action, which registers new user*/
    function user_register($data){
        $query="insert into `user`(`username`,`firstname`,`lastname`,`email`,`password`)values(:username,:firstname,:lastname,:email,:password)";
        $d=([
        'username'=> $data['username'],
        'firstname' => $data['firstname'],
        'lastname'=> $data['lastname'],
        'email'=> $data['mail'],
        'password'=> $data['passw'],
        ]);
        $this->DBManager->do_query_db($query,$d);
        mkdir("./uploads/".$data['username']);
    }
    
    /*function called by controller login_action, which logins an user*/
    function user_login($username){
        $data = $this->get_user_by_username($username);
        $_SESSION['username']=$data['username'];
        $_SESSION['id']=$data['id'];
    }
    
    /*function called by controller logout_action, which logouts an user*/
    function user_logout(){
        session_unset();
        session_destroy();
        return true;
    }
}