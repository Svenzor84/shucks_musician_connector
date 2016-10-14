<?php
/**
* Filename:    log_user.php
* Author:      Steve Ross-Byers
* Description: PHP file that logs a user in (or out), and redirects the user depending on success or failure
*/

require_once('db_access.php');
session_start();

//if a user is logged in, log that user out and redirect to the homepage
if (isset($_SESSION[username])) {
    
    session_destroy();
    
    //set the message to acknowledge successfull log out
    $mess[] = 1;
    
    header('Location: ' . $base_url . '?' . http_build_query($mess));
    exit();
    
}

//if the POST variable is empty, redirect the user to the shucks! home page
if (empty($_POST)) {
    
    header('Location: ' . $base_url);
    exit();
    
}

//login form validation
if (empty($_POST[username])) {
    
    //if username field is empty, add the error message to the message array
    $mess[] = 2;
    
//if the field is not empty, add the value to the message array
} else {
    
    $mess[username] = $_POST[username];
}

//login form validation
if (empty($_POST[password])) {
    
    //if password field is empty, add the error message to the message array
    $mess[] = 3;
    
//if the field is not empty, add the value to the message array
} else {
    
    $mess[password] = $_POST[password];
}

//if any of the previous messages were set, redirect the user with the proper messages
if (isset($mess[0])) {
    
    //redirect the user to the login page
    header('Location: ' . $base_url . 'login.php/?' . http_build_query($mess));
    exit();
    
}

//attempt to log in the user with the given data and redirect the user depending on the outcome
if (user_login($_POST[username], $_POST[password])) {
    
    //if the user is successfully logged in, unset the previous messages and set up the proper message and redirect the user to their profile
    unset($mess);
    
    //if the user has just created their account and been successfully logged in (mess array contains 0)
    if(in_array(0, $_GET)) {
        
        //set the new user message to appear
        $mess[] = 0;
        
    //otherwise, treat this as a normal login
    } else {
        
        //and set the normal login message to appear
        $mess[] = 1;
    }
    
    header('Location: ' . $base_url . 'my_profile.php/?' . http_build_query($mess));
    exit();
    
} else {
    
    //if the login was unsuccessful, add the proper meessage to the message array
    $mess[0] = 1;
    
    //redirect the user to the login page
    header('Location: ' . $base_url . 'login.php/?' . http_build_query($mess));
    exit();

    
}
