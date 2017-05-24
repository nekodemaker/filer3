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
                        $message="This file is uploaded now";
                    } else {
                        $error="This File is too big !";
                    }
                }else{
                    $error="This File already exists, you can't upload the same file !";
                }
            }
        }
        $allFiles=$fileManager->get_all_files_by_id($_SESSION['id']);
        echo $this->renderView('home.html.twig', ['id' => $_SESSION['id'],'username'=>$_SESSION['username'],'message'=>$message,'error' => $error,'allFiles'=>$allFiles]);
    }
}