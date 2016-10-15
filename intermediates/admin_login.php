<?php
/**
* Filename:    admin_login.php
* Author:      Steve Ross-Byers
* Description: Intermediate login page for Admin function access
*/

require_once('db_access.php');
session_start();

if (!isset($_SESSION[username])) {
    
    //Redirect users who are not logged in back to the home page
    header("Location: " . $base_url);
    exit();
    
//if the POST variable is empty, redirect the user to the shucks! home page
} else if (empty($_POST)) {
    
    //add the proper value to the message array
    $mess[] = 3;
    
    //redirect the user to their profile page
    header('Location: ' . $base_url . 'my_profile.php/?' . http_build_query($mess));
    exit();
    
}

//use the user_login function to check the given password and ensure that the user is indeed an admin before allowing access
//if the login step fails
if (!user_login($_POST[adminUsername], $_POST[adminPassword])) {
    
    //add the proper value to the message array
    $mess[] = 2;
    
    //redirect the user to their profile page
    header('Location: ' . $base_url . 'my_profile.php/?' . http_build_query($mess));
    exit();
    
//otherwise, if the user is not an admin
} else if (!$_SESSION[is_admin] == 1){
    
    //add the proper value to the message array
    $mess[] = 3;
    
    //redirect the user to their profile page
    header('Location: ' . $base_url . 'my_profile.php/?' . http_build_query($mess));
    exit();
    
//otherwise, we can allow access to the admin function area
} else {
    
    //redirect the user to admin function page
    header('Location: ' . $base_url . 'admin_func.php');
    exit();
}