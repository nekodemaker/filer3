<?php

namespace Controller;

use Model\UserManager;


class DefaultController extends BaseController
{
    public function homeAction()
    {
        if (!empty($_SESSION['id']))
        {
            echo $this->renderView('home.html.twig',
                                   ['id' => $_SESSION['id'],'username'=>$_SESSION['username']]);
        }
        else {
            echo $this->renderView('home.html.twig',
                                   []);
        }
    }
    
}