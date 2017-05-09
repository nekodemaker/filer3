<?php

require_once('model/db.php');

//function which returns the data of username from user
function get_user_by_username($username){
    $data = find_query_result("select * from `user` where `username`= :username",
    ['username' => $username]);
    return $data;
}
//function which returns true if the mail already exists in database, else returns false
function is_mail_already_exist($mail){
    $data = find_query_result("select * from `user` where `email`= :mail",
    ['mail' => $mail]);
    return (count($data) != 0);
}
//Function which takes $data(eventually $_POST) and returns true if username and password corresponds in the database
function user_check_login($data){
    if (empty($data['username']) OR empty($data['passw']))
        return false;
    $result = find_query_result("select * from `user` where `username`= :username and `password`= :password",
    ['username'=> $data['username'],'password'=> $data['passw']]);
    if(count($result)!=1){
        return false;
    }
    return true;
}
//Function which takes $data(eventually $_POST) and returns true if data entries correspond in the database
function user_check_register($data){
    if (empty($data['username']) OR empty($data['firstname']) OR empty($data['lastname']) OR empty($data['passw']) OR empty($data['mail']))
        return 1;
    if(count(get_user_by_username($data['username']))!=0){
        return 2;
    }
    if(is_mail_already_exist($data['mail'])){
        return 3;
    }
    return 0;
}

//FUNCTIONS CALLED BY CONTROLLERS

/*function called by controller login_action, which logins an user*/
function user_login($username){
    $data = get_user_by_username($username);
    $_SESSION['username']=$data[0]['username'];
    $_SESSION['id']=$data[0]['id'];
}
/*function called by controller logout_action, which logouts an user*/
function user_logout(){
    session_unset();
    session_destroy();
    return true;
}
/*function called by controller register_action, which registers new user*/
function user_register($data){
    $query="insert into `user`(`username`,`firstname`,`lastname`,`email`,`password`)values(:username,:firstname,:lastname,:email,:password)";
    $d=([
    'username'=> $data['username'],
    'firstname' => $data['firstname'],
    'lastname'=> $data['lastname'],
    'email'=> $data['mail'],
    'password'=> $data['passw'],
    ]);
    do_query_db($query,$d);
    mkdir("./uploads/".$data['username']);
}
/*function called by controller upload_action, which uploads a file*/
function user_upload($data,$fileName,$file_url){
    if(move_uploaded_file($data["monfichier"]["tmp_name"],$file_url)) {
        //If copy, then put the file url,date,userid into database
        $userId=$_SESSION['id'];
        $q="insert into `file`(`user_id`,`file_name`,`file_url`,`date_creation`)values(:userId,:fileName,:urlName,NOW())";
        put_data_into_database($q, [
        'userId'=> $userId,
        'fileName' => $fileName,
        'urlName'=> $file_url,
        ]);
        return true;
    }else{
        return false;
    }
}

//FUNCTIONS ABOUT MESSAGES

/*function display an error message that contain in $_SESSION['error']  */
function errors_file(){
    if(!empty($_SESSION['error'])){
        echo '<p>'.$_SESSION['error'].'</p>';
        unset($_SESSION['error']);
    }
}
/*function display a message that contain in $_SESSION['message']  */
function good_messages(){
    if(!empty($_SESSION['message'])){
        echo '<p>'.$_SESSION['message'].'</p>';
        unset($_SESSION['message']);
    }
}


//function which returns a file data row by filename and user id from table file
function get_file_by_filename_from_id($filename,$id){
    $q="select * from `file` where `user_id`= :userid and `file_name`= :filename ";
    $result=find_query_result($q,[
    'userid'=> $id,
    'filename'=> $filename,
    ]);
    return $result;
}
//function which returns all directories rows from table directory
function get_all_directories_by_id($id){
    $q="select * from `directory` where `id_user`= :userid";
    $result=find_query_result($q,[
    'userid'=> $id,
    ]);
    return $result;
}
//function return all directories without his own sub-directories
function get_all_directories_by_id_for_move_dir($id,$directoryUrl){
    $result=get_all_directories_by_id($id);
    $res=[];
    $size=count($result);
    $sizeDirectoryUrl=strlen($directoryUrl);
    for ($i=0;$i<$size;$i++) {
        if(strncmp($result[$i]['directory_url'],$directoryUrl,$sizeDirectoryUrl) != false){
            $res[]=$result[$i];
        }
    }
    return $res;
}

//FUNCTIONS OF DISPLAYING EDIT TEXT OR VISUALIZING

/*function which displays editing zone to modify a text file's contents*/
function edit_appear(){
    if(!empty($_SESSION['open-file-edit'])){
        echo "<div id='myModal' class='modal'>";
        echo "<div class='modal-content'>";
        echo "<span class='close'>&times;</span>";
        
        echo "<form method='post'  action='?action=editFile'>";
        echo "<input type='hidden' name='url-file' value='".$_SESSION['open-file-edit']."'>";
        echo "<textarea name='file-content'>".file_get_contents($_SESSION['open-file-edit'])."</textarea>";
        echo '<input type="submit" id="edit-ok" value="ok Edit File"></form>';
        echo "</div></div>";
        unset($_SESSION['open-file-edit']);
    }
}
function visualize_appear_text($fileUrl){
    echo "<div id='myModal' class='modal'>";
    echo "<div class='modal-content'>";
    echo "<span class='close'>&times;</span>";
    echo "<textarea name='file-content'>".file_get_contents($fileUrl)."</textarea>";
    echo "</div></div>";
}
function visualize_appear_video($fileUrl){
    echo "<div id='myModal' class='modal'>";
    echo "<div class='modal-content'>";
    echo "<span class='close'>&times;</span>";
    echo '<video width="320" height="240" src="'.$fileUrl.'" controls>';
    echo '</video>';
    echo "</div></div>";
}
function visualize_appear_music($fileUrl){
    echo "<div id='myModal' class='modal'>";
    echo "<div class='modal-content'>";
    echo "<span class='close'>&times;</span>";
    echo "<audio src='".$fileUrl."' controls>";
    echo "</audio>";
    echo "</div></div>";
}
function visualize_appear_picture($fileUrl){
    echo "<div id='myModal' class='modal'>";
    echo "<div class='modal-content'>";
    echo "<span class='close'>&times;</span>";
    echo '<img src="'.$fileUrl.'">';
    echo "</div></div>";
}
function visualize_appear_application($fileUrl){
    echo "<div id='myModal' class='modal'>";
    echo "<div class='modal-content'>";
    echo "<span class='close'>&times;</span>";
    echo '<embed src="'.$fileUrl.'">';
    echo "</div></div>";
}
/*function which displays visualization of file's contents depend of type*/
function visualize_appear(){
    if(!empty($_SESSION['mime-file'])){
        $urlFile=$_SESSION['mime-file'][0];
        switch ($_SESSION['mime-file'][1]) {
            case "text":
                visualize_appear_text($urlFile);
                break;
            case "video":
                visualize_appear_video($urlFile);
                break;
            case "music":
                visualize_appear_music($urlFile);
                break;
            case "picture":
                visualize_appear_picture($urlFile);
                break;
            case "application":
                visualize_appear_application($urlFile);
                break;
    }
    unset($_SESSION['mime-file']);
}
}

//FUNCTIONS DISPLAY_ALL_DIRECTORIES NEEDS

/*function which returns html content of list of directories contents*/
function get_directory_content($directoryUrl){
    $cont=scandir($directoryUrl);
    $result="<ul>";
    foreach($cont as $key => $value){
        if($value !=="." && $value !==".."){
            if(is_dir($directoryUrl."/".$value)){
                $result.="<li class='dir'>".$value."</li>";
            }else{
                $result.="<li class='fil'>".$value."</li>";
            }
        }
    }
    $result.="</ul>";
    return $result;
}

//FUNCTIONS DISPLAY_ALL_FILES NEEDS

/*function which returns all user files from id*/
function get_all_files_by_id($id){
    $q="select * from `file` where `user_id`= :userid";
    $result=find_query_result($q,[
    'userid'=> $id,
    ]);
    return $result;
}
/*function which returns html content of formular with button to edit a text file*/
function put_button_edit($fileUrl){
    $fileName=basename($fileUrl);
    $fileExtension=pathinfo($fileName,PATHINFO_EXTENSION);
    $res="";
    if($fileExtension=="txt"){
        $res.='<form method="post" action="?action=openEditFile">';
        $res.='<input type="hidden" name="file-url-edit" value="'.$fileUrl.'">';
        $res.='<input type="submit" class="edit-file" value="Edit File">';
        $res.='</form>';
    }
    return $res;
}
/*function which returns html content of formular with button to visualize some types of files*/
function put_button_visualize($fileUrl){
    $type=["text","audio","video","image","application"];
    $fileName=basename($fileUrl);
    $fileMime=mime_content_type($fileUrl);
    $fileType=explode('/',$fileMime);
    $res="";
    if(in_array($fileType[0],$type)){
        $res.='<form method="post" action="?action=visualizeFile">';
        $res.='<input type="hidden" name="file-url-visualize" value="'.$fileUrl.'">';
        $res.='<input type="submit" class="visualize-file" value="Visualize File">';
        $res.='</form>';
    }
    return $res;
}

//FUNCTIONS DISPLAY_ALL_FILES AND DISPLAY_ALL_DIRECTORIES NEED

/*function which returns html content of multiple choice list of directories paths*/
function multiple_choice_list_html($type,$directoryUrl){
    if($type==="f"){
        $r=get_all_directories_by_id($_SESSION['id']);
    }else{
        $r=get_all_directories_by_id_for_move_dir($_SESSION['id'],$directoryUrl);
    }
    
    $result='<select name="directory-choice">';
    foreach ($r as $row) {
        $result.='<option>'.$row['directory_url'].'</option>';
    }
    $result.='</select>';
    return $result;
}

//FUNCTIONS WHICH DISPLAY FILES AND DIRECTORIES

/*function which displays user's files  with action's buttons*/
function display_all_user_files(){
    $result=get_all_files_by_id($_SESSION['id']);
    echo '<div class="block-name">';
    echo '<p>File name</p><p>Url</p><p>Date of creation</p><p>Actions</p></div>';
    foreach ($result as $row) {
        echo  '<div class="file-block">';
        echo  '<div class="file-name">'.$row['file_name'].'</div>';
        echo  '<div class="file-url">'.$row['file_url'].'</div>';
        echo  '<div class="file-date-creation">'.$row['date_creation'].'</div>';
        echo  '<div class="buttons"><div class="rename-block"><input type="button" class="rename-file" value="Rename File"></div>';
        echo  '<form method="post" action="?action=delete">';
        echo  '<input type="hidden" name="file-url" value="'.$row['file_url'].'">';
        echo  '<input type="submit" name="delete-file" value="Delete File">';
        echo  '</form>';
        echo  '<div class="replace-block"><input type="button" class="replace-file" value="Replace File"></div>';
        echo  '<form method="post" action="?action=download">';
        echo  '<input type="hidden" name="url-file" value="'.$row['file_url'].'">';
        echo  '<input type="hidden" name="name-user" value="'.$_SESSION['username'].'">';
        echo  '<input type="submit" name="download-file" value="Download File">';
        echo  '</form>';
        echo  '<div hidden class="list-directories">'.multiple_choice_list_html("f","").'</div>';
        echo  '<div class="move-file-block"><input type="button" class="move-file" value="Move File"></div>';
        echo  put_button_edit($row['file_url']);
        echo  put_button_visualize($row['file_url']);
        echo  '</div></div>';
    }
}
/*function which displays directories with action's buttons*/
function display_all_user_directories(){
    $result=get_all_directories_by_id($_SESSION['id']);
    echo '<div class="block-name">';
    echo '<p>Directory name</p><p>URL</p><p>Directory content</p><p>Actions</p></div>';
    foreach ($result as $row) {
        echo  '<div class="directory-block">';
        echo  '<div class="directory-name">'.$row['directory_name'].'</div>';
        echo  '<div class="directory-url">'.$row['directory_url'].'</div>';
        echo  '<div class="directory-content">'.get_directory_content($row['directory_url']).'</div>';
        echo  '<div class="buttons-dir"><div class="rename-block-dir"><div hidden class="url-parent">'.$row['parent_url'].'</div><input type="button" class="rename-directory" value="Rename Directory"></div>';
        echo  '<div class="create-block-dir"><input type="button" class="create-directory" value="Create Directory"></div>';
        echo  '<form method="post" action="?action=deleteDirectory">';
        echo  '<input type="hidden" name="directory-name" value="'.$row['directory_name'].'">';
        echo  '<input type="hidden" name="url-directory" value="'.$row['directory_url'].'">';
        echo  '<input type="submit" name="delete-directory" value="Delete Directory">';
        echo  '</form>';
        echo  '<div hidden class="list-directories-dir">'.multiple_choice_list_html("d",$row['directory_url']).'</div>';
        echo  '<div class="move-directory-block"><input type="button" class="move-directory" value="Move Directory"></div>';
        echo  '</div>';
        echo  '</div>';
    }
}