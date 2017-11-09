<?php

namespace Controller;

use Model\FileManager;
use Model\DirectoryManager;
use Model\LogManager;

class DirectoryController extends BaseController
{
    function createNewFolderAction(){
        $directoryManager = DirectoryManager::getInstance();
        $logManager = LogManager::getInstance();
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            //If user put special characters
            if($logManager->test_special_char($_POST['new_directory'])){
                $logManager->log_security($_SESSION['username'],"put dangerous characters on create new folder action");
            }else{
                if($directoryManager->create_new_folder("./uploads/".$_SESSION['username']."/",$_POST['new_directory'])){
                    $logManager->log_access($_SESSION['username'],"created directory ".$_POST['new_directory']);
                    $_SESSION['message']="Directory ".$_POST['new_directory']." is created";
                }else{
                    $_SESSION['error']="Another directory with that name exists there, please chose another name";
                }
            }
        }
        $this->redirect('home');
    }
    
    function createDirectoryAction(){
        $directoryManager = DirectoryManager::getInstance();
        $logManager = LogManager::getInstance();
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            if(!$logManager->test_path($_POST['url-directory'],$_SESSION['username'])){
                $logManager->log_security($_SESSION['username'],"modified url directory by: ".$_POST['url-directory']);
            }elseif(($logManager->test_special_char($_POST['url-directory'])==1)||($logManager->test_special_char($_POST['directory_name'])==1)){
                $logManager->log_security($_SESSION['username'],"put dangerous characters on create directory inside directory action");
            }else{
                if($directoryManager->create_new_folder($_POST['url-directory']."/",$_POST['directory_name'])){
                    $logManager->log_access($_SESSION['username'],"created directory ".$_POST['directory_name']." into ".$_POST['url-directory']);
                    $_SESSION['message']="Directory ".$_POST['directory_name']." is created inside the directory ".$_POST['url-directory'];
                }else{
                    $_SESSION['error']="Another directory with that name exists there, please chose another name";
                }
            }
        }
        $this->redirect('home');
    }
    
    function deleteDirectoryAction(){
        $directoryManager = DirectoryManager::getInstance();
        $logManager = LogManager::getInstance();
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            if(!$logManager->test_path($_POST['url-directory'],$_SESSION['username'])){
                $logManager->log_security($_SESSION['username'],"modified url directory by: ".$_POST['url-directory']);
            }elseif($logManager->test_special_char($_POST['url-directory'])==1){
                $logManager->log_security($_SESSION['username'],"put dangerous characters on delete directory action");
            }else{
                if(file_exists($_POST['url-directory'])){
                    $directoryManager->delete_directory($_POST['url-directory']);
                    $logManager->log_access($_SESSION['username']," deleted directory ".$_POST['url-directory']);
                    $_SESSION['message']="Directory ".$_POST['directory-name']." is deleted";
                }else{
                    $_SESSION['error']="Problem with deleting the directory ".$_POST['url-directory'];
                }
            }
        }
        $this->redirect('home');
    }
    
    function renameDirectoryAction(){
        $directoryManager = DirectoryManager::getInstance();
        $logManager = LogManager::getInstance();
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            if(!$logManager->test_path($_POST['url-directory'],$_SESSION['username'])||!$logManager->test_path($_POST['url-parent'],$_SESSION['username'])){
                $logManager->log_security($_SESSION['username'],"modified url file by: ".$_POST['url-file']);
            }elseif(($logManager->test_special_char($_POST['url-directory'])==1)||($logManager->test_special_char($_POST['directory-rename'])==1)||($logManager->test_special_char($_POST['url-parent'])==1)){
                $logManager->log_security($_SESSION['username'],"put dangerous characters on rename directory action");
            }else{
                if($directoryManager->rename_directory($_POST['url-directory'],$_POST['url-parent'],$_POST['directory-rename'])){
                    $logManager->log_access($_SESSION['username'],"renamed directory ".$_POST['url-directory']." into ".$_POST['directory-rename']);
                    $_SESSION['message']="Directory ".$_POST['url-directory']." is renamed";
                }else{
                    $_SESSION['error']="the directory ".$_POST['url-directory']." already exists, choose another name !";
                }
            }
        }
        $this->redirect('home');
    }
    
    function moveDirectoryAction(){
        $directoryManager = DirectoryManager::getInstance();
        $logManager = LogManager::getInstance();
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            if(!$logManager->test_path($_POST['url-directory'],$_SESSION['username'])||!$logManager->test_path($_POST['list-directories-dir'],$_SESSION['username'])){
                $logManager->log_security($_SESSION['username'],"modified url directory by: ".$_POST['url-directory']);
            }elseif(($logManager->test_special_char($_POST['url-directory'])==1)||($logManager->test_special_char($_POST['list-directories-dir'])==1)){
                $logManager->log_security($_SESSION['username'],"put dangerous characters on move directory action");
            }else{
                if($directoryManager->move_directory($_POST['url-directory'],$_POST['list-directories-dir'])){
                    $logManager->log_access($_SESSION['username']," moved directory ".$_POST['url-directory']." into ".$_POST['list-directories-dir']);
                    $_SESSION['message']="Your Directory ".$_POST['url-directory']." is moved to ".$_POST['list-directories-dir'];
                }else{
                    $_SESSION['error']="the directory ".$_POST['url-directory']." already exists, don't move !";
                }
            }
        }
        $this->redirect('home');
    }
    
}