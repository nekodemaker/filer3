<?php

namespace Controller;

use Model\UserManager;
use Model\FileManager;
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
            $fileManager = FileManager::getInstance();
            $allFiles=$fileManager->get_all_files_by_id($_SESSION['id']);
            echo $this->renderView('home.html.twig',
            ['id' => $_SESSION['id'],'username'=>$_SESSION['username'],'error'=>$error,'message'=>$message,'allFiles'=>$allFiles]);
        }
        else {
            echo $this->renderView('home.html.twig',
            []);
        }
    }
    
}