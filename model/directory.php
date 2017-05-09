<?php

require_once('model/db.php');

//FUNCTIONS CALL THE Database
/*function which insert directory data into database*/
function insert_directory_into_db($name,$url,$urlParent){
    $q="insert into `directory`(`directory_name`,`id_user`,`directory_url`,`parent_url`)values(:name,:id,:url,:urlParent)";
    do_query_db($q,[
    'name'=> $name,
    'id' => $_SESSION['id'],
    'url'=> $url,
    'urlParent'=> $urlParent,
    ]);
}
/*function which deletes directory data from database*/
function delete_directory_from_db($urlDirectory){
    $q="delete from `directory` where `id_user`= :id and `directory_url`= :url ";
    do_query_db($q,[
    'id' => $_SESSION['id'],
    'url'=> $urlDirectory,
    ]);
}
/*function which delete file data from database*/
function delete_file_from_db($urlFile){
    $q="delete from `file` where `user_id`= :id and `file_url`= :url ";
    do_query_db($q,[
    'id' => $_SESSION['id'],
    'url'=> $urlFile,
    ]);
}
/*function which updates directory data in database*/
function update_rename_directory_from_db($urlDirectory,$newUrlDirectory){
    $q="update `directory` set `directory_url`= :directoryurl,`parent_url`= :parent where `id_user`= :userid and `directory_url`= :oldname";
    do_query_db($q,[
    'directoryurl' => $newUrlDirectory,
    'parent' => dirname($newUrlDirectory),
    'userid'=> $_SESSION['id'],
    'oldname'=> $urlDirectory,
    ]);
}
/*function which updates file data in database*/
function update_rename_file_from_db($urlFile,$newUrlFile){
    $q="update `file` set `file_url`= :fileurl where `user_id`= :userid and `file_url`= :oldname";
    do_query_db($q,[
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
                    rename_directory_rec($directoryUrl."/".$value,$newUrlDirectory."/".$value);
                }else{
                    update_rename_file_from_db($directoryUrl."/".$value,$newUrlDirectory."/".$value);
                }
            }
        }
        update_rename_directory_from_db($directoryUrl,$newUrlDirectory);
    }else{
        update_rename_file_from_db($directoryUrl,$newUrlDirectory);
    }
}

//FUNCTIONS CALLED BY CONTROLLERS

/*function called by controller createNewFolder_action, which creates a new directory in user principal folder*/
function create_new_folder($directoryUrl,$nameDirectory){
    if(is_dir($directoryUrl.$nameDirectory)){
        return false;
    }else{
        insert_directory_into_db($nameDirectory,$directoryUrl.$nameDirectory,$directoryUrl);
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
                    delete_directory($directoryUrl."/".$value);
                }else{
                    delete_file_from_db($directoryUrl."/".$value);
                    unlink($directoryUrl."/".$value);
                }
            }
        }
        delete_directory_from_db($directoryUrl);
        rmdir($directoryUrl."/");
    }else{
        delete_file_from_db($directoryUrl);
        unlink($directoryUrl);
    }
}
/*function called by controller renameDirectory_action, which renames a  directory*/
function rename_directory($urlDirectory,$urlParentDirectory,$newName){
    if(!file_exists($urlParentDirectory.$newName)){
        $q="update `directory` set `directory_name`= :name , `directory_url`= :directoryurl where `id_user`= :userid and `directory_url`= :oldname";
        do_query_db($q,[
        'name'=> $newName,
        'directoryurl' => $urlParentDirectory.$newName,
        'userid'=> $_SESSION['id'],
        'oldname'=> $urlDirectory,
        ]);
        rename_directory_rec($urlDirectory,$urlParentDirectory.$newName);
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
        rename_directory_rec($directoryUrl,$directoryUrlDestination."/".basename($directoryUrl));
        rename($directoryUrl,$directoryUrlDestination."/".basename($directoryUrl));
        return true;
    }
}