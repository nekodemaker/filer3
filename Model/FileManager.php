<?php
namespace Model;
error_reporting(~E_DEPRECATED);
class FileManager
{
    private $DBManager;
    
    private static $instance = null;
    public static function getInstance()
    {
        if (self::$instance === null)
            self::$instance = new FileManager();
        return self::$instance;
    }
    
    private function __construct()
    {
        $this->DBManager = DBManager::getInstance();
    }
    
    /*function called by controller upload_action, which uploads a file*/
    function user_upload($data,$fileName,$file_url){
        if(move_uploaded_file($data["monfichier"]["tmp_name"],$file_url)) {
            //If copy, then put the file url,date,userid into database
            $userId=$_SESSION['id'];
            $q="insert into `file`(`user_id`,`file_name`,`file_url`,`date_creation`)values(:userId,:fileName,:urlName,NOW())";
            $this->DBManager->do_query_db($q, [
            'userId'=> $userId,
            'fileName' => $fileName,
            'urlName'=> $file_url,
            ]);
            return true;
        }else{
            return false;
        }
    }
    
    /*function which returns all user files from id*/
    function get_all_files_by_id($id){
        $q="select * from `file` where `user_id`= :userid";
        $result=$this->DBManager->findAllSecure($q,[
        'userid'=> $id,
        ]);
        return $result;
    }
}