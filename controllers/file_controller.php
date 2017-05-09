<?php

require_once('model/file.php');
require_once('model/log.php');

function delete_action(){
    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        //TEST IF $_POST['file-url'] begins with  ./uploads/"username"
        if(!test_path($_POST['file-url'],$_SESSION['username'])){
            log_security($_SESSION['username'],"modified url file by: ".$_POST['file-url']);
        }elseif(test_special_char($_POST['file-url'])==1){
            log_security($_SESSION['username'],"put dangerous characters on delete action");
        }else{
            if(delete_file($_POST['file-url'])){
                log_access($_SESSION['username'],"deleted file ".$_POST['file-url']);
                $_SESSION['message']=basename($_POST['file-url'])." is deleted now";
            }else{
                $_SESSION['error']="File can't be deleted, because doesn't exist";
            }
        }
        
    }
    header('Location: ?action=userconnected');
}

function rename_action(){
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        if(!test_path($_POST['url-file'],$_SESSION['username'])){
            log_security($_SESSION['username'],"modified url file by: ".$_POST['url-file']);
        }elseif((test_special_char($_POST['url-file'])==1)||(test_special_char($_POST['rename'])==1)||(test_special_char($_POST['name-file'])==1)){
            log_security($_SESSION['username'],"put dangerous characters on rename file action");
        }else{
            $parentUrl=dirname($_POST['url-file']);
            if(rename_file($parentUrl."/".$_POST['rename'],$parentUrl."/".$_POST['name-file'])){
                log_access($_SESSION['username'],"renamed file ".$_POST['name-file']." into ".$_POST['rename']);
                $_SESSION['message']="Your file is renamed to ".$_POST['rename'];
            }else{
                $_SESSION['error']="Another file with that name exists, please chose another name";
            }
        }
    }
    header('Location: ?action=userconnected');
}

function replace_action(){
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(!test_path($_POST['url-file'],$_SESSION['username'])){
            log_security($_SESSION['username'],"modified url file by: ".$_POST['url-file']);
        }else if(test_special_char($_POST['url-file'])==1){
            log_security($_SESSION['username'],"put dangerous characters on replace file action");
        }else{
            $newNameFile = $_FILES['monfichier']['name'];
            $directoryDest=dirname($_POST['url-file'])."/";
            //Before, test if the filename already exists in the database
            if(!file_exists($directoryDest.$newNameFile)){
                if(replace_file($_POST['url-file'],$newNameFile)) {
                    log_access($_SESSION['username'],"replaced file ".$_POST['monfichier']." into ".$newNameFile);
                    $_SESSION['message']=$_POST['name-file']." is replaced by ".$newNameFile;
                } else {
                    $_SESSION['error']="Your new file is too big ! Please chose another one";
                }
            }else{
                $_SESSION['error']="This File already exists, you can't replace with an already uploaded file !";
            }
        }
    }
    header('Location: ?action=userconnected');
}

function download_action(){
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(!test_path($_POST['url-file'],$_SESSION['username'])){
            log_security($_SESSION['username'],"modified url file by: ".$_POST['url-file']);
        }elseif(test_special_char($_POST['url-file'])==1){
            log_security($_SESSION['username'],"put dangerous characters into file-url on download action");
        }else{
            /*That code is taken from php.net*/
            if (!download_file($_POST['url-file'])) {
                $_SESSION['error']="You can't download with a ghost file !";
            }
        }
    }
    
    header('Location: ?action=userconnected');
}

function moveFile_action(){
    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        if(!test_path($_POST['url-file'],$_SESSION['username'])||!test_path($_POST['directory-choice'],$_SESSION['username'])){
            log_security($_SESSION['username'],"modified url file by: ".$_POST['url-file']);
        }elseif((test_special_char($_POST['url-file'])==1)||(test_special_char($_POST['directory-choice'])==1)){
            log_security($_SESSION['username'],"put dangerous characters on move file action");
        }else{
            if(move_file($_POST['url-file'],$_POST['directory-choice'])){
                log_access($_SESSION['username'],"moved file ".$_POST['url-file']." into ".$_POST['directory-choice']);
                $_SESSION['message']="Your file is".$_POST['url-file']." and directory ".$_POST['directory-choice'];
                $_SESSION['message']="Your file ".basename($_POST['url-file'])." is moved to ".$_POST['directory-choice'];
            }else{
                $_SESSION['error']="Another file with that name exists in the directory ".$_POST['directory-choice'].", so don't move";
            }
        }
    }
    header('Location: ?action=userconnected');
}

function openEditFile_action(){
    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        if(!test_path($_POST['file-url-edit'],$_SESSION['username'])){
            log_security($_SESSION['username'],"modified url file on openEditFile by: ".$_POST['file-url-edit']);
        }else if((test_special_char($_POST['url-file'])==1)||(test_special_char($_POST['file-url-edit'])==1)){
            log_security($_SESSION['username'],"put dangerous characters on open edit file action");
        }else{
            if(open_edit_file($_POST['file-url-edit'])){
                $_SESSION['message']="Please edit your file".basename($_POST['file-url-edit']);
            }else{
                $_SESSION['error']="Problem, the file you want to edit doesn't exist";
            }
        }
    }
    header('Location: ?action=userconnected');
}

function editFile_action(){
    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        if(!test_path($_POST['url-file'],$_SESSION['username'])){
            log_security($_SESSION['username'],"modified url file on editFile by: ".$_POST['url-file']);
        }elseif(test_special_char($_POST['url-file'])==1){
            log_security($_SESSION['username'],"put dangerous characters on edit file action");
        }else{
            if(edit_file($_POST['url-file'],$_POST['file-content'])){
                log_access($_SESSION['username'],"edited text file ".$_POST['url-file']);
                $_SESSION['message']="Your file ".$_POST['url-file']." is edited";
            }else{
                $_SESSION['error']="Problem, your file ".$_POST['url-file']." can't be edited";
            }
        }
    }
    header('Location: ?action=userconnected');
}

function visualizeFile_action(){
    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        if(!test_path($_POST['file-url-visualize'],$_SESSION['username'])){
            log_security($_SESSION['username'],"modified url file by: ".$_POST['url-file']);
        }elseif(test_special_char($_POST['file-url-visualize'])==1){
            log_security($_SESSION['username'],"put dangerous characters on visualize file action");
        }else{
            if(visualize_file($_POST['file-url-visualize'])){
                $_SESSION['message']="Your file ".basename($_POST['file-url-visualize'])." is visualized";
            }else{
                $_SESSION['error']="Problem, your file ".basename($_POST['file-url-visualize'])." can't be visualized";
            }
        }
    }
    header('Location: ?action=userconnected');
}