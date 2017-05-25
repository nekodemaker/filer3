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
    
    function update_url_file_move($newFileUrl,$oldFileUrl){
    $q="update `file` set `file_url`= :fileurl where `user_id`= :userid and `file_url`= :oldfile";
    $this->DBManager->do_query_db($q,[
    'fileurl' => $newFileUrl,
    'userid'=> $_SESSION['id'],
    'oldfile'=> $oldFileUrl,
    ]);
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
        /*function called by controller moveFile_action, which moves a file into another directory */
        function move_file($fileUrl,$directoryUrlDestination){
            if(file_exists($directoryUrlDestination."/".basename($fileUrl))){
                return false;
            }else{
                $this->update_url_file_move($directoryUrlDestination."/".basename($fileUrl),$fileUrl);
                rename($fileUrl,$directoryUrlDestination."/".basename($fileUrl));
                return true;
            }
        }
        
        /*function called by controller openEditFile_action, which displays an edit zone for file text */
        function open_edit_file($fileUrl){
            if(file_exists($fileUrl)){
                $_SESSION["open-file-edit"]=$fileUrl;
                return true;
            }else{
                return false;
            }
        }
        /*function called by controller editFile_action, which edits file text */
        function edit_file($fileUrl,$fileContent){
            if(file_put_contents($fileUrl,$fileContent)){
                return true;
            }else{
                return false;
            }
        }
        /*function called by controller visualizeFile_action, which visualize a certaint type of file */
        function visualize_file($fileUrl){
            $typeMime=explode('/',mime_content_type($fileUrl));
            $bool=true;
            switch ($typeMime[0]) {
                case "text":
                    $_SESSION["mime-file"]=[$fileUrl,"text"];
                    break;
                case "audio":
                    $_SESSION["mime-file"]=[$fileUrl,"music"];
                    break;
                case "video":
                    $_SESSION["mime-file"]=[$fileUrl,"video"];
                    break;
                case "image":
                    $_SESSION["mime-file"]=[$fileUrl,"picture"];
                    break;
                case "application":
                    $_SESSION["mime-file"]=[$fileUrl,"application"];
                    break;
                default:
                    $bool=false;
                    break;
        }
        return $bool;
    }
    
    /*function which returns all user files from id*/
    function get_all_files_by_id($id){
        $q="select * from `file` where `user_id`= :userid";
        $result=$this->DBManager->findAllSecure($q,[
        'userid'=> $id,
        ]);
        return $result;
    }
    
    
    function getAllFilesForDisplay($id){
        $type=["text","audio","video","image","application"];
        $allFiles=$this->get_all_files_by_id($id);
        $result=[];
        
        for($i=0;$i<count($allFiles);$i++){
            $fileName=basename($allFiles[$i]['file_url']);
            $fileExtension=pathinfo($fileName,PATHINFO_EXTENSION);
            $fileMime=mime_content_type($allFiles[$i]['file_url']);
            $fileType=explode('/',$fileMime);
            $visualization=false;
            $edit=false;
            if(in_array($fileType[0],$type)){
                $visualization=true;
            }
            if($fileExtension=="txt"){
                $edit=true;
            }
            $elem=array("file"=>$allFiles[$i],"extension"=>$fileExtension,"mime"=>$fileType[0],"visualization"=>$visualization,"edit"=>$edit);
            array_push($result,$elem);
        }
        return $result;
    }
    }
