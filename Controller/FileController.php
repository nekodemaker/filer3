<?php

namespace Controller;

use Model\FileManager;
use Model\LogManager;

class FileController extends BaseController
{
    function uploadAction(){
        
        $fileManager = FileManager::getInstance();
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $message="";
            $error="";
            $fileManager = FileManager::getInstance();
            $logManager = LogManager::getInstance();
            
            $nameFile = $_FILES['monfichier']['name'];
            $modifiedNameFile=$_POST["new-file-upload"];
            //test if name file is modified
            if((!empty($modifiedNameFile))&&($nameFile != $modifiedNameFile)){
                $nameFile=$modifiedNameFile;
            }
            if($logManager->test_special_char($nameFile)==1){
                $logManager->log_security($_SESSION['username'],"put dangerous characters on move directory action");
            }else{
                $directoryDest = "./uploads/".$_SESSION['username']."/";
                //Before, test if the filename already exists in the database
                if(!file_exists($directoryDest.$nameFile)){
                    if($fileManager->user_upload($_FILES,$nameFile,$directoryDest.$nameFile)) {
                        $logManager->log_access($_SESSION['username'],"uploaded file ".$nameFile);
                        $_SESSION['message']="This file is uploaded now";
                    } else {
                        $_SESSION['error']="This File is too big !";
                    }
                }else{
                    $_SESSION['error']="This File already exists, you can't upload the same file !";
                }
            }
        }
        $this->redirect('home');
    }
    
    function deleteAction(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $fileManager = FileManager::getInstance();
            $logManager = LogManager::getInstance();
            //TEST IF $_POST['file-url'] begins with  ./uploads/"username"
            if(!$logManager->test_path($_POST['file-url'],$_SESSION['username'])){
                $logManager->log_security($_SESSION['username'],"modified url file by: ".$_POST['file-url']);
            }elseif($logManager->test_special_char($_POST['file-url'])==1){
                $logManager->log_security($_SESSION['username'],"put dangerous characters on delete action");
            }else{
                if($fileManager->delete_file($_POST['file-url'])){
                    $logManager->log_access($_SESSION['username'],"deleted file ".$_POST['file-url']);
                    $_SESSION['message']=basename($_POST['file-url'])." is deleted now";
                }else{
                    $_SESSION['error']="File can't be deleted, because doesn't exist";
                }
            }
            
        }
        $this->redirect('home');
    }
    
    function renameAction(){
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $fileManager = FileManager::getInstance();
            $logManager = LogManager::getInstance();
            if(!$logManager->test_path($_POST['url-file'],$_SESSION['username'])){
                $logManager->log_security($_SESSION['username'],"modified url file by: ".$_POST['url-file']);
            }elseif(($logManager->test_special_char($_POST['url-file'])==1)||($logManager->test_special_char($_POST['rename'])==1)||($logManager->test_special_char($_POST['name-file'])==1)){
                $logManager->log_security($_SESSION['username'],"put dangerous characters on rename file action");
            }else{
                $parentUrl=dirname($_POST['url-file']);
                if($fileManager->rename_file($parentUrl."/".$_POST['rename'],$parentUrl."/".$_POST['name-file'])){
                    $logManager->log_access($_SESSION['username'],"renamed file ".$_POST['name-file']." into ".$_POST['rename']);
                    $_SESSION['message']="Your file is renamed to ".$_POST['rename'];
                }else{
                    $_SESSION['error']="Another file with that name exists, please chose another name";
                }
            }
        }
        $this->redirect('home');
    }
    
    function replaceAction(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $fileManager = FileManager::getInstance();
            $logManager = LogManager::getInstance();
            if(!$logManager->test_path($_POST['url-file'],$_SESSION['username'])){
                $logManager->log_security($_SESSION['username'],"modified url file by: ".$_POST['url-file']);
            }else if($logManager->test_special_char($_POST['url-file'])==1){
                $logManager->log_security($_SESSION['username'],"put dangerous characters on replace file action");
            }else{
                $newNameFile = $_FILES['monfichier']['name'];
                $directoryDest=dirname($_POST['url-file'])."/";
                //Before, test if the filename already exists in the database
                if(!file_exists($directoryDest.$newNameFile)){
                    if($fileManager->replace_file($_POST['url-file'],$newNameFile)) {
                        $logManager->log_access($_SESSION['username'],"replaced file ".$_POST['monfichier']." into ".$newNameFile);
                        $_SESSION['message']=$_POST['name-file']." is replaced by ".$newNameFile;
                    } else {
                        $_SESSION['error']="Your new file is too big ! Please chose another one";
                    }
                }else{
                    $_SESSION['error']="This File already exists, you can't replace with an already uploaded file !";
                }
            }
        }
        $this->redirect('home');
    }
    
    function downloadAction(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $fileManager = FileManager::getInstance();
            $logManager = LogManager::getInstance();
            if(!$logManager->test_path($_POST['url-file'],$_SESSION['username'])){
                $logManager->log_security($_SESSION['username'],"modified url file by: ".$_POST['url-file']);
            }elseif($logManager->test_special_char($_POST['url-file'])==1){
                $logManager->log_security($_SESSION['username'],"put dangerous characters into file-url on download action");
            }else{
                /*That code is taken from php.net*/
                if (!$fileManager->download_file($_POST['url-file'])) {
                    $_SESSION['error']="You can't download with a ghost file !";
                }
            }
        }
        $this->redirect('home');
    }
    
    function moveFileAction(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $fileManager = FileManager::getInstance();
            $logManager = LogManager::getInstance();
            if(!$logManager->test_path($_POST['url-file'],$_SESSION['username'])||!$logManager->test_path($_POST['directory-choice'],$_SESSION['username'])){
                $logManager->log_security($_SESSION['username'],"modified url file by: ".$_POST['url-file']);
            }elseif(($logManager->test_special_char($_POST['url-file'])==1)||($logManager->test_special_char($_POST['directory-choice'])==1)){
                $logManager->log_security($_SESSION['username'],"put dangerous characters on move file action");
            }else{
                if($fileManager->move_file($_POST['url-file'],$_POST['directory-choice'])){
                    $logManager->log_access($_SESSION['username'],"moved file ".$_POST['url-file']." into ".$_POST['directory-choice']);
                    $_SESSION['message']="Your file is".$_POST['url-file']." and directory ".$_POST['directory-choice'];
                    $_SESSION['message']="Your file ".basename($_POST['url-file'])." is moved to ".$_POST['directory-choice'];
                }else{
                    $_SESSION['error']="Another file with that name exists in the directory ".$_POST['directory-choice'].", so don't move";
                }
            }
        }
        $this->redirect('home');
    }
    
    function openEditFileAction(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $fileManager = FileManager::getInstance();
            $logManager = LogManager::getInstance();
            if(!$logManager->test_path($_POST['file-url-edit'],$_SESSION['username'])){
                $logManager->log_security($_SESSION['username'],"modified url file on openEditFile by: ".$_POST['file-url-edit']);
            }else if(($logManager->test_special_char($_POST['url-file'])==1)||($logManager->test_special_char($_POST['file-url-edit'])==1)){
                $logManager->log_security($_SESSION['username'],"put dangerous characters on open edit file action");
            }else{
                if($fileManager->open_edit_file($_POST['file-url-edit'])){
                    $_SESSION['message']="Please edit your file".basename($_POST['file-url-edit']);
                }else{
                    $_SESSION['error']="Problem, the file you want to edit doesn't exist";
                }
            }
        }
        $this->redirect('home');
    }
    
    function editFileAction(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $fileManager = FileManager::getInstance();
            $logManager = LogManager::getInstance();
            if(!$logManager->test_path($_POST['url-file'],$_SESSION['username'])){
                $logManager->log_security($_SESSION['username'],"modified url file on editFile by: ".$_POST['url-file']);
            }elseif($logManager->test_special_char($_POST['url-file'])==1){
                $logManager->log_security($_SESSION['username'],"put dangerous characters on edit file action");
            }else{
                if($fileManager->edit_file($_POST['url-file'],$_POST['file-content'])){
                    $logManager->log_access($_SESSION['username'],"edited text file ".$_POST['url-file']);
                    $_SESSION['message']="Your file ".$_POST['url-file']." is edited";
                }else{
                    $_SESSION['error']="Problem, your file ".$_POST['url-file']." can't be edited";
                }
            }
        }
        $this->redirect('home');
    }
    
    function visualizeFileAction(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $fileManager = FileManager::getInstance();
            $logManager = LogManager::getInstance();
            if(!$logManager->test_path($_POST['file-url-visualize'],$_SESSION['username'])){
                $logManager->log_security($_SESSION['username'],"modified url file by: ".$_POST['url-file']);
            }elseif($logManager->test_special_char($_POST['file-url-visualize'])==1){
                $logManager->log_security($_SESSION['username'],"put dangerous characters on visualize file action");
            }else{
                if($fileManager->visualize_file($_POST['file-url-visualize'])){
                    $_SESSION['message']="Your file ".basename($_POST['file-url-visualize'])." is visualized";
                }else{
                    $_SESSION['error']="Problem, your file ".basename($_POST['file-url-visualize'])." can't be visualized";
                }
            }
        }
        $this->redirect('home');
    }
}