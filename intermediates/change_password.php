<?php
/**
* Filename:    change_password.php
* Author:      Steve Ross-Byers
* Description: Page that changes a user's password, either through the user's profile page (with POST)
*              or via admin password reset button (with GET)
*/

require_once('db_access.php');
session_start();

if (!isset($_SESSION[username])) {
    
    //Redirect users who are not logged in back to the home page
    header("Location: " . $base_url);
    exit();
    
}

//if the POST variable is set (user initiated) we can use the values to try and change the password
if (!empty($_POST)) {
    
    //validate the change password form
    if (md5($_POST[oldPassword]) != $_SESSION[password]) {
        
        //Redirect users to their profile page with an error message
        header("Location: " . $base_url . "my_profile.php/?0=4");
        exit();
    }
    
    if ($_POST[newPassword] != $_POST[confirmPassword]) {
        
        //Redirect user to their profile page with an error message
        header("Location: " . $base_url . "my_profile.php/?0=5");
        exit();
    }
    
    if (empty($_POST[newPassword])) {
        
        //Redirect user to their profile page with an error message
        header("Location: " . $base_url . "my_profile.php/?0=6");
        exit();
    }
    
    //if we made it through validation, we can change the user's password in the database
    //call the set_password function and pass in the new password and user_id
    set_password($_SESSION[user_id], $_POST[newPassword]);
    
    //set up a variable with the user's email address and name
    //$to = $_SESSION[f_name] . ' ' . $_SESSION[l_name] . '<' . $_SESSION[email_address] . '>';
    $to = $_SESSION[email_address];
    
    //this variable gets the subject line of our email
    $subject = "Shucks! Password Changed";
    
    //next we set up the body of the email
    $message = "Hello, and thank you for using Shucks!\r\n
                Your password has been changed from your profile page.  If this was intentional, no further action is needed.  If you suspect someone has changed your password without your knowledge, please log in immediately with your new password (below) and change it to a new password.\r\n
                \r\n
                Username: " . $_SESSION[username] . "\r\n
                Old Password: " . $_POST[oldPassword] . "\r\n
                New Password: " . $_POST[newPassword] . "\r\n
                \r\n
                Thank you again, we hope that you enjoy Shucks!, now get out there and make some noise!!!";
                
    //dont forget to wordwrap the message variable
    $message = wordwrap($message, 70, "\r\n");
    
    //finally, we need the additional headers
    $headers = "From: " . $return_address . "\r\n" .
               "Reply-To: " . $return_address . "\r\n" .
               "X-Mailer: PHP/" . phpversion();
    
    //now we can assemble and send our email
    mail($to, $subject, $message, $headers);
    
    //update the session data to reflect the password change
    $_SESSION[password] = md5($_POST[newPassword]);
    
    //redirect the user to their profile page with a message containing the new password
    //NOTE: Once we have the ability to email user's their new password, this redirect will change so that the new password is not passed via GET nor displayed on the resulting page
    header("Location: " . $base_url . "my_profile.php/?pass=" . $_POST[newPassword]);
    
//otherwise, we are using the GET variable, and must ensure that an admin is attempting to reset another user's password
} else if(isset($_GET)) {
    
    //this function is not available yet, because sending email from cloud 9 is disabled
    //when email funcitonality is set up this part of the function will change the user's password to a random string and email it to them
    //at this point, the user is simply redirected to the admin functions page (since this is an admin function) with a message
    header("Location: " . $base_url . "admin_func.php/?0=0");
    
//if neither of the variables are set
} else {
    
    //Redirect users to their profile page with an error message
    header("Location: " . $base_url . "my_profile.php/?0=3");
    exit();
}