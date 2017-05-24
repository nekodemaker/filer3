<?php

namespace Controller;

use Model\UserManager;
use Model\LogManager;

class SecurityController extends BaseController
{
    
    function loginAction()
    {
        if(!isset($_SESSION['id'])){
            $error = '';
            $userManager = UserManager::getInstance();
            $logManager = LogManager::getInstance();
            if ($_SERVER['REQUEST_METHOD'] === 'POST')
            {
                //If user put special characters into login and/or password
                if(($logManager->test_special_char($_POST['username'])==1)||($logManager->test_special_char($_POST['passw'])==1)){
                    $logManager->log_security("someone","put dangerous characters into login's input's areas");
                    $this->redirect('home');
                }
                if ($userManager->user_check_login($_POST))
                {
                    $userManager->user_login($_POST['username']);
                    $this->redirect('home');
                }
                else {
                    $error = "Username or  password false !";
                }
            }
            echo $this->renderView('login.html.twig', ['error' => $error]);
        }else{
            $this->redirect('home');
        }
    }


function logoutAction()
{   
    $userManager = UserManager::getInstance();
    if ($userManager->user_logout())
    {
        $this->redirect('home');
    }
}
    
    public function registerAction()
    {
        if(!isset($_SESSION['id'])){
            $error = '';
            if ($_SERVER['REQUEST_METHOD'] === 'POST')
            {
                $userManager = UserManager::getInstance();
                $logManager = LogManager::getInstance();
                if(($logManager->test_special_char($_POST['username'])==1)||($logManager->test_special_char($_POST['firstname'])==1)||($logManager->test_special_char($_POST['lastname'])==1)||($logManager->test_special_char($_POST['passw'])==1)||($logManager->test_special_char($_POST['mail'])==1)){
                    $logManager->log_security("someone","put dangerous characters into registers's input's areas");
                    $this->redirect('home');
                }
                $check=$userManager->user_check_register($_POST);
                if ($check===0)
                {
                    $userManager->user_register($_POST);
                    $logManager->log_access($_POST['username'],"registered");
                    $this->redirect('home');
                }
                else if($check===1){
                    $error = "At least one of the fields is empty";
                }else if($check===2){
                    $error = "The username adress already exists, choose another one";
                }else{
                    $error = "The mail already exists, choose another one";
                }
            }
            echo $this->renderView('register.html.twig', ['error' => $error]);
        }else{
            $this->redirect('home');
        }
    }
}