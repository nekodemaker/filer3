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
            $fileManager = FileManager::getInstance();
            $allFiles=$fileManager->get_all_files_by_id($_SESSION['id']);
            echo $this->renderView('home.html.twig',
                                   ['id' => $_SESSION['id'],'username'=>$_SESSION['username'],'allFiles'=>$allFiles]);
        }
        else {
            echo $this->renderView('home.html.twig',
                                   []);
        }
    }
    
}