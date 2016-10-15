<?php
/**
* Filename:    remove_user.php
* Author:      Steve Ross-Byers
* Description: Intermediate user removal page, Admin Function
*/

require_once('db_access.php');
session_start();

if (!isset($_SESSION[username])) {
    
    //Redirect users who are not logged in back to the home page
    header("Location: " . $base_url);
    exit();
    
} else if ($_SESSION[is_admin] != 1 || !isset($_GET[user])) {
    
    //add the proper value to the message array
    $mess[] = 3;
    
    //redirect the user to their profile page
    header('Location: ' . $base_url . 'my_profile.php/?' . http_build_query($mess));
    exit();
}

//now that we have passed validation, we can call the remove user function from the db_access script
if (remove_user($_GET[user])) {
    
    //if the user was successfully removed, redirect the user to the admin function page and acknowledge success
    header("Location: " . $base_url . "admin_func.php/?0=1");
    exit();
    
} else {
    
    //if the user removal failed, redirect the user to the admin function page and acknowledge failure
    header("Location: " . $base_url . "admin_func.php/?0=2");
    exit();
}