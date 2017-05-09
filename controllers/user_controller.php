<?php

require_once('model/user.php');
require_once('model/log.php');

function login_action()
{
    if(!isset($_SESSION['id'])){
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            //If user put special characters into login and/or password
            if((test_special_char($_POST['username'])==1)||(test_special_char($_POST['passw'])==1)){
                log_security("someone","put dangerous characters into login's input's areas");
                header('Location: ?action=home');
            }
            if (user_check_login($_POST))
            {
                user_login($_POST['username']);
                header('Location: ?action=userconnected');
            }
            else {
                $error = "Username or  password false !";
            }
        }
        require('views/login.php');
    }else{
        header('Location: ?action=userconnected');
    }
}

function logout_action()
{
    if (user_logout())
    {
        header('Location: ?action=home');
    }
}

function register_action()
{
    if(!isset($_SESSION['id'])){
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            if((test_special_char($_POST['username'])==1)||(test_special_char($_POST['firstname'])==1)||(test_special_char($_POST['lastname'])==1)||(test_special_char($_POST['passw'])==1)||(test_special_char($_POST['mail'])==1)){
                log_security("someone","put dangerous characters into registers's input's areas");
                header('Location: ?action=home');
            }
            $check=user_check_register($_POST);
            if ($check===0)
            {
                user_register($_POST);
                log_access($_POST['username'],"registered");
                header('Location: ?action=home');
            }
            else if($check===1){
                $error = "At least one of the fields is empty";
            }else if($check===2){
                $error = "The username adress already exists, choose another one";
            }else{
                $error = "The mail already exists, choose another one";
            }
        }
        require('views/register.php');
    }else{
        header('Location: ?action=userconnected');
    }
}

function userconnected_action(){
    if(!empty($_SESSION['id'])){
        require('views/userconnected.php');
    } else{
        header('Location: ?action=home');
    }
}

function upload_action(){
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        $nameFile = $_FILES['monfichier']['name'];
        $modifiedNameFile=$_POST["new-file-upload"];
        //test if name file is modified
        if((!empty($modifiedNameFile))&&($nameFile != $modifiedNameFile)){
            $nameFile=$modifiedNameFile;
        }
        if(test_special_char($nameFile)==1){
            log_security($_SESSION['username'],"put dangerous characters on move directory action");
        }else{
        $directoryDest = "./uploads/".$_SESSION['username']."/";
        //Before, test if the filename already exists in the database
        if(!file_exists($directoryDest.$nameFile)){
            if(user_upload($_FILES,$nameFile,$directoryDest.$nameFile)) {
                log_access($_SESSION['username'],"uploaded file ".$nameFile);
                $_SESSION['message']="This file is uploaded now";
            } else {
                $_SESSION['error']="This File is too big !";
            }
        }else{
            $_SESSION['error']="This File already exists, you can't upload the same file !";
        }
        }
    }
    
    header('Location: ?action=userconnected');
}