<?php

namespace Controller;

use Model\UserManager;
use Model\FileManager;
use Model\DirectoryManager;
use Model\LogManager;

class DefaultController extends BaseController
{
    public function homeAction()
    {
        if (!empty($_SESSION['id']))
        {
            if(!empty($_SESSION['error'])){
                $error=$_SESSION['error'];
                unset($_SESSION['error']);
            }else{
                $error="";
            }
            if(!empty($_SESSION['message'])){
                $message=$_SESSION['message'];
                unset($_SESSION['message']);
            }else{
                $message="";
            }
            if(!empty($_SESSION['open-file-edit'])){
                $openFileEdit=$_SESSION['open-file-edit'];
                $fileGetContentsOpenFileEdit=file_get_contents($_SESSION['open-file-edit']);
                unset($_SESSION['open-file-edit']);
            }else{
                $openFileEdit="";
                $fileGetContentsOpenFileEdit="";
            }
            $fileManager = FileManager::getInstance();
            $directoryManager = DirectoryManager::getInstance();
            $allFiles=$fileManager->getAllFilesForDisplay($_SESSION['id']);
            $allDirectories=$directoryManager->get_all_directories_by_id($_SESSION['id']);;
            echo $this->renderView('home.html.twig',
            ['id' => $_SESSION['id'],'username'=>$_SESSION['username'],'error'=>$error,'message'=>$message,'openFileEdit'=>$openFileEdit,'fileGetContentsOpenFileEdit'=>$fileGetContentsOpenFileEdit,'allFiles'=>$allFiles,'allDirectories'=>$allDirectories]);
        }
        else {
            echo $this->renderView('home.html.twig',
            []);
        }
    }
    
}