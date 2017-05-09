<?php

require_once('model/directory.php');
require_once('model/log.php');

function createNewFolder_action(){
    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        //If user put special characters
        if(test_special_char($_POST['new_directory'])){
            log_security($_SESSION['username'],"put dangerous characters on create new folder action");
        }else{
            if(create_new_folder("./uploads/".$_SESSION['username']."/",$_POST['new_directory'])){
                log_access($_SESSION['username'],"created directory ".$_POST['new_directory']);
                $_SESSION['message']="Directory ".$_POST['new_directory']." is created";
            }else{
                $_SESSION['error']="Another directory with that name exists there, please chose another name";
            }
        }
    }
    header('Location: ?action=userconnected');
}

function createDirectory_action(){
    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        if(!test_path($_POST['url-directory'],$_SESSION['username'])){
            log_security($_SESSION['username'],"modified url directory by: ".$_POST['url-directory']);
        }elseif((test_special_char($_POST['url-directory'])==1)||(test_special_char($_POST['directory_name'])==1)){
            log_security($_SESSION['username'],"put dangerous characters on create directory inside directory action");
        }else{
            if(create_new_folder($_POST['url-directory']."/",$_POST['directory_name'])){
                log_access($_SESSION['username'],"created directory ".$_POST['directory_name']." into ".$_POST['url-directory']);
                $_SESSION['message']="Directory ".$_POST['directory_name']." is created inside the directory ".$_POST['url-directory'];
            }else{
                $_SESSION['error']="Another directory with that name exists there, please chose another name";
            }
        }
    }
    header('Location: ?action=userconnected');
}

function deleteDirectory_action(){
    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        if(!test_path($_POST['url-directory'],$_SESSION['username'])){
            log_security($_SESSION['username'],"modified url directory by: ".$_POST['url-directory']);
        }elseif(test_special_char($_POST['url-directory'])==1){
            log_security($_SESSION['username'],"put dangerous characters on delete directory action");
        }else{
            if(file_exists($_POST['url-directory'])){
                delete_directory($_POST['url-directory']);
                log_access($_SESSION['username']," deleted directory ".$_POST['url-directory']);
                $_SESSION['message']="Directory ".$_POST['directory-name']." is deleted";
            }else{
                $_SESSION['error']="Problem with deleting the directory ".$_POST['url-directory'];
            }
        }
    }
    header('Location: ?action=userconnected');
}

function renameDirectory_action(){
    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        if(!test_path($_POST['url-directory'],$_SESSION['username'])||!test_path($_POST['url-parent'],$_SESSION['username'])){
            log_security($_SESSION['username'],"modified url file by: ".$_POST['url-file']);
        }elseif((test_special_char($_POST['url-directory'])==1)||(test_special_char($_POST['directory-rename'])==1)||(test_special_char($_POST['url-parent'])==1)){
            log_security($_SESSION['username'],"put dangerous characters on rename directory action");
        }else{
            if(rename_directory($_POST['url-directory'],$_POST['url-parent'],$_POST['directory-rename'])){
                log_access($_SESSION['username'],"renamed directory ".$_POST['url-directory']." into ".$_POST['directory-rename']);
                $_SESSION['message']="Directory ".$_POST['url-directory']." is renamed";
            }else{
                $_SESSION['error']="the directory ".$_POST['url-directory']." already exists, choose another name !";
            }
        }
    }
    header('Location: ?action=userconnected');
}

function moveDirectory_action(){
    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        if(!test_path($_POST['url-directory'],$_SESSION['username'])||!test_path($_POST['directory-choice'],$_SESSION['username'])){
            log_security($_SESSION['username'],"modified url directory by: ".$_POST['url-directory']);
        }elseif((test_special_char($_POST['url-directory'])==1)||(test_special_char($_POST['directory-choice'])==1)){
            log_security($_SESSION['username'],"put dangerous characters on move directory action");
        }else{
            if(move_directory($_POST['url-directory'],$_POST['directory-choice'])){
                log_access($_SESSION['username']," moved directory ".$_POST['url-directory']." into ".$_POST['directory-choice']);
                $_SESSION['message']="Your Directory ".$_POST['url-directory']." is moved to ".$_POST['directory-choice'];
            }else{
                $_SESSION['error']="the directory ".$_POST['url-directory']." already exists, don't move !";
            }
        }
    }
    header('Location: ?action=userconnected');
}