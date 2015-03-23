<?php
/*
 * TODO: Make note of all the places that will need adjusting to make sure that this all works on the school's system
 * too.
 */

//TODO: Remove before submitting
// FOR DEBUG
/*ini_set('display_errors', 1);
error_reporting(E_ALL);*/


session_start();


#############
# VARIABLES #
#############



//require_once('FirePHPCore/FirePHP.class.php');
include_once("index.php");


include_once('functions.php');



/*ob_start();
if (!$firephp) {
    ob_start();

    $firephp = FirePHP::getInstance(true);
}*/

// If the register_new value in the GET is set and == 1, display the form for registering a new user.
/*if(isset($_GET['register_new']) && $_GET['register_new'] == 1) {

    register_display();
}

if (isset($_POST['username']) && isset($_POST['password'])) {

    user_cred($_POST['username'], $_POST['password']);
}


if(isset($_GET['new_use']) && $_GET['new_use'] ==1){


    $user_name = $_POST['name'];
    $user_email = $_POST['email'];
    $user_pw = $_POST['password'];

    new_user($user_name,$user_email,$user_pw);
    ob_clean();
    $url = "http://" . $_SERVER['HTTP_HOST'] . "/final2_back_01/index.php?i";

    header("Location: " . $url) or die("didn't redirect from login");
}*/




