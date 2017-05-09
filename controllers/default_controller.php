<?php

require_once('model/user.php');

/*When the user is not connected, redirection in the same page with action=login*/
function home_action()
{
    if (!empty($_SESSION['id']))
    {
        require('views/userconnected.php');
    }
    else {
        require('views/home.php');
    }
}