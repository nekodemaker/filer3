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
    
    /*function called by controller delete_action, which deletes a file */
    function delete_file($fileUrl){
        if(file_exists($fileUrl)){
            $q="delete from `file` where `user_id`= :userid and `file_url`= :fileurl ";
            $this->DBManager->do_query_db($q,[
            'userid'=> $_SESSION['id'],
            'fileurl'=> $fileUrl,
            ]);
            unlink($fileUrl);
            return true;
        }else{
            return false;
        }
    }
    //function called by controller rename_action, renames the file, if a file with that new name already exists,just puts an error message
    function rename_file($newFileUrl,$oldFileUrl){
        if(!file_exists($newFileUrl)){
            $q="update `file` set `file_name`= :filename , `file_url`= :fileurl where `user_id`= :userid and `file_url`= :oldfileurl";
            $this->DBManager->do_query_db($q,[
            'filename'=> basename($newFileUrl),
            'fileurl' => $newFileUrl,
            'userid'=> $_SESSION['id'],
            'oldfileurl'=>$oldFileUrl,
            ]);
            rename($oldFileUrl, $newFileUrl);
            return true;
        }else{
            return false;
        }
    }
    /*function called by controller replace_action, which replaces a file */
    function replace_file($actualFileUrl,$newFileName){
        $parentUrl=dirname($actualFileUrl)."/";
        if($this->user_upload($_FILES,$newFileName,$parentUrl.$newFileName)) {
            $this->delete_file($actualFileUrl);
            return true;
        } else {
            return false;
        }
    }
    /*function called by controller download_action, which download a file */
    function download_file($fileUrl){
        /*That code is taken from php.net*/
        if (file_exists($fileUrl)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($fileUrl).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fileUrl));
            readfile($fileUrl);
            exit;
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