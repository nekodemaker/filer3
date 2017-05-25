<?php
namespace Model;

class DirectoryManager
{
    private $DBManager;
    
    private static $instance = null;
    public static function getInstance()
    {
        if (self::$instance === null)
            self::$instance = new DirectoryManager();
        return self::$instance;
    }
    
    private function __construct()
    {
        $this->DBManager = DBManager::getInstance();
    }
    
    
    //FUNCTIONS CALL THE Database
    /*function which insert directory data into database*/
    function insert_directory_into_db($name,$url,$urlParent){
        $q="insert into `directory`(`directory_name`,`id_user`,`directory_url`,`parent_url`)values(:name,:id,:url,:urlParent)";
        $this->DBManager->do_query_db($q,[
        'name'=> $name,
        'id' => $_SESSION['id'],
        'url'=> $url,
        'urlParent'=> $urlParent,
        ]);
    }
    /*function which deletes directory data from database*/
    function delete_directory_from_db($urlDirectory){
        $q="delete from `directory` where `id_user`= :id and `directory_url`= :url ";
        $this->DBManager->do_query_db($q,[
        'id' => $_SESSION['id'],
        'url'=> $urlDirectory,
        ]);
    }
    /*function which delete file data from database*/
    function delete_file_from_db($urlFile){
        $q="delete from `file` where `user_id`= :id and `file_url`= :url ";
        $this->DBManager->do_query_db($q,[
        'id' => $_SESSION['id'],
        'url'=> $urlFile,
        ]);
    }
    /*function which updates directory data in database*/
    function update_rename_directory_from_db($urlDirectory,$newUrlDirectory){
        $q="update `directory` set `directory_url`= :directoryurl,`parent_url`= :parent where `id_user`= :userid and `directory_url`= :oldname";
        $this->DBManager->do_query_db($q,[
        'directoryurl' => $newUrlDirectory,
        'parent' => dirname($newUrlDirectory),
        'userid'=> $_SESSION['id'],
        'oldname'=> $urlDirectory,
        ]);
    }
    /*function which updates file data in database*/
    function update_rename_file_from_db($urlFile,$newUrlFile){
        $q="update `file` set `file_url`= :fileurl where `user_id`= :userid and `file_url`= :oldname";
        $this->DBManager->do_query_db($q,[
        'fileurl' => $newUrlFile,
        'userid'=> $_SESSION['id'],
        'oldname'=> $urlFile,
        ]);
    }
    /*function which updates the directory and all its contents in database*/
    function rename_directory_rec($directoryUrl,$newUrlDirectory){
        if(is_dir($directoryUrl)){
            $cont=scandir($directoryUrl);
            foreach($cont as $key => $value){
                if($value !=="." && $value !==".."){
                    if(is_dir($directoryUrl."/".$value)){
                        $this->rename_directory_rec($directoryUrl."/".$value,$newUrlDirectory."/".$value);
                    }else{
                        $this->update_rename_file_from_db($directoryUrl."/".$value,$newUrlDirectory."/".$value);
                    }
                }
            }
            $this->update_rename_directory_from_db($directoryUrl,$newUrlDirectory);
        }else{
            $this->update_rename_file_from_db($directoryUrl,$newUrlDirectory);
        }
    }
    
    
    //function which returns all directories rows from table directory
    function get_all_directories_by_id($id){
        $q="select * from `directory` where `id_user`= :userid";
        $result=$this->DBManager->findAllSecure($q,[
        'userid'=> $id,
        ]);
        return $result;
    }
    
    //function return all directories without his own sub-directories
    function get_all_directories_by_id_for_move_dir($id,$directoryUrl){
        $result=$this->get_all_directories_by_id($id);
        $res=[];
        $size=count($result);
        $sizeDirectoryUrl=strlen($directoryUrl);
        for ($i=0;$i<$size;$i++) {
            
            if(strncmp($result[$i]['directory_url'],$directoryUrl,$sizeDirectoryUrl) != false){
                $res[]=$result[$i];
            }
        }
        return $res;
    }
    
    //function return the content as an array of directory
    function get_directory_content($directoryUrl){
        $cont=scandir($directoryUrl);
        $result=[];
        foreach($cont as $key => $value){
            if($value !=="." && $value !==".."){
                if(is_dir($directoryUrl."/".$value)){
                    $elem=array("type"=>"dir","name"=>$value);
                }else{
                    $elem=array("type"=>"file","name"=>$value);
                }
                array_push($result,$elem);
            }
        }
        return $result;
    }
    
    //FUNCTIONS CALLED BY CONTROLLERS
    
    /*function called by controller createNewFolder_action, which creates a new directory in user principal folder*/
    function create_new_folder($directoryUrl,$nameDirectory){
        if(is_dir($directoryUrl.$nameDirectory)){
            return false;
        }else{
            $this->insert_directory_into_db($nameDirectory,$directoryUrl.$nameDirectory,$directoryUrl);
            mkdir($directoryUrl.$nameDirectory);
            return true;
        }
    }
    /*function called by controller deleteDirectory_action, which deletes a  directory*/
    function delete_directory($directoryUrl){
        if(is_dir($directoryUrl)){
            $cont=scandir($directoryUrl);
            foreach($cont as $key => $value){
                if($value !=="." && $value !==".."){
                    if(is_dir($directoryUrl."/".$value)){
                        $this->delete_directory($directoryUrl."/".$value);
                    }else{
                        $this->delete_file_from_db($directoryUrl."/".$value);
                        unlink($directoryUrl."/".$value);
                    }
                }
            }
            $this->delete_directory_from_db($directoryUrl);
            rmdir($directoryUrl."/");
        }else{
            $this->delete_file_from_db($directoryUrl);
            unlink($directoryUrl);
        }
    }
    /*function called by controller renameDirectory_action, which renames a  directory*/
    function rename_directory($urlDirectory,$urlParentDirectory,$newName){
        if(!file_exists($urlParentDirectory.$newName)){
            $q="update `directory` set `directory_name`= :name , `directory_url`= :directoryurl where `id_user`= :userid and `directory_url`= :oldname";
            $this->DBManager->do_query_db($q,[
            'name'=> $newName,
            'directoryurl' => $urlParentDirectory.$newName,
            'userid'=> $_SESSION['id'],
            'oldname'=> $urlDirectory,
            ]);
            $this->rename_directory_rec($urlDirectory,$urlParentDirectory.$newName);
            rename($urlDirectory,$urlParentDirectory.$newName);
            return true;
        }else{
            return false;
        }
    }
    /*function called by controller moveDirectory_action, which moves a  directory into another directory*/
    function move_directory($directoryUrl,$directoryUrlDestination){
        if(file_exists($directoryUrlDestination."/".basename($directoryUrl))){
            return false;
        }else{
            $this->rename_directory_rec($directoryUrl,$directoryUrlDestination."/".basename($directoryUrl));
            rename($directoryUrl,$directoryUrlDestination."/".basename($directoryUrl));
            return true;
        }
    }
    
    /*function which return array with necessary datas for display*/
    function getAllDirectoriesForDisplay($id){
        $allDirectories=$this->get_all_directories_by_id($id);
        $result=[];
        
        for($i=0;$i<count($allDirectories);$i++){
            $content=$this->get_directory_content($allDirectories[$i]['directory_url']);
            $moveDirectoryList=$this->get_all_directories_by_id_for_move_dir($id,$allDirectories[$i]['directory_url']);
            
            $elem=array("directory"=>$allDirectories[$i],"content"=>$content,"list_move"=>$moveDirectoryList);
            array_push($result,$elem);
        }
        return $result;
    }
}