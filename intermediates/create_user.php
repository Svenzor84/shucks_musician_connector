<?php
/**
* Filename:    create_user.php
* Author:      Steve Ross-Byers
* Description: PHP file that creates a new user and redirects the user depending on success or failure
*/

require_once('db_access.php');
session_start();

//set up regular expressions for checking validity of user entries
$username_pattern = "/^(?![_.])(?!.*[_.]{2})(?=.*[A-Za-z])[\w.]+(?<![_.])$/";
$name_pattern = "/^(?![- ])(?!.*[- ]{2})(?!.*[']{2})(?=.*[A-Za-z])[A-Za-z- ']+(?<![- ])$/";


//if the POST variable is empty or a user is already logged in, redirect the user to the shucks! home page
if (empty($_POST) || isset($_SESSION[username])) {
    
    header('Location: ' . $base_url);
    exit();
    
}

//create user form validation
if (empty($_POST[new_username])) {
    
    //if the new username field is empty, add the proper value to the message array
    $mess[] = 4;
    
} else {
    
    //if the new username field is NOT empty, save the contents of the field
    $mess[new_username] = "'" . $_POST[new_username] . "'";
    
    //check to ensure that the input value is in valid username structure and length
    if (strlen($_POST[new_username]) < 6 || strlen($_POST[new_username]) > 30 || !preg_match($username_pattern, $_POST[new_username])) {
        
        //if the username is too long or too short, or doesn't match the regex pattern, add the proper value to the message array
        $mess[] = 11;
        
    //if the new username passes validation, we can check to see if the username is unique
    } else {
        
        //build a query to find usernames in the database that matches the new username
        $sql = "SELECT *
                FROM users
                WHERE (username = :username)";
                
        //prepare the query statement
        $statement = $db->prepare($sql);
    
        //bind arguments as parameters and execute the query
        $statement->execute([
        
            ':username' => $_POST[new_username],
            
        ]);
        
        //if our result has at least one row, the username is not unique to the database
        if ($statement->rowcount() > 0) {
        
            //set the proper value to the message array
            $mess[] = 13;
        }
    }
}

if (empty($_POST[new_email])) {
    
    //if the new email field is empty, add the proper value to the message array
    $mess[] = 5;
    
} else {
    
    //if the new email field is NOT empty, save the contents of the field
    $mess[new_email] = "'" . $_POST[new_email] . "'";
    
    //check to ensure that the input value is in valid email address structure and length
    if (strlen($_POST[new_email]) > 200 || !filter_var($_POST[new_email], FILTER_VALIDATE_EMAIL)) {
        
        //if the email address is too long or doesn't pass the filter validation, add the proper value to the message array
        $mess[] = 12;
    }
}

if (empty($_POST[f_name])) {
    
    //if the first name field is empty, add the proper value to the message array
    $mess[] = 6;
    
} else {
    
    //if the first name field is NOT empty, save the contents of the field
    $mess[f_name] = "'" . $_POST[f_name] . "'";
    
    //check to ensure that the input value is in valid name structure and length
    if (strlen($_POST[f_name]) < 1 || strlen($_POST[f_name]) > 30 || !preg_match($name_pattern, $_POST[f_name])) {
        
        //if the first name is too long or too short, or doesn't match the regex pattern, add the proper value to the message array
        $mess[] = 14;
    }
}

if (!empty($_POST[m_name])) {
    
    //if the middle name field is not empty, save the contents of the field
    $mess[m_name] = "'" . $_POST[m_name] . "'";
    
    //check to ensure that the input value is in valid name structure and length
    if (strlen($_POST[m_name]) < 1 || strlen($_POST[m_name]) > 30 || !preg_match($name_pattern, $_POST[m_name])) {
        
        //if the middle name is too long or too short, or doesn't match the regex pattern, add the proper value to the message array
        $mess[] = 15;
    }
}

if (empty($_POST[l_name])) {
    
    //if the last name field is empty, add the proper value to the message array
    $mess[] = 7;
    
} else {
    
    //if the last name field is NOT empty, save the contents of the field
    $mess[l_name] = "'" . $_POST[l_name] . "'";
    
    //check to ensure that the input value is in valid name structure and length
    if (strlen($_POST[l_name]) < 1 || strlen($_POST[l_name]) > 30 || !preg_match($name_pattern, $_POST[l_name])) {
        
        //if the first name is too long or too short, or doesn't match the regex pattern, add the proper value to the message array
        $mess[] = 16;
    }
}

if (!empty($_POST[suffix])) {
    
    //if the last name field is not empty, save the contents of the field
    $mess[suffix] = "'" . $_POST[suffix] . "'";
    
}

if (empty($_POST[new_password])) {
    
    //if the new password field is empty, add the proper value to the message array
    $mess[] = 8;
    
} else {
    
    //if the new password field is NOT empty, save the contents of the field
    $mess[new_password] = "'" . $_POST[new_password] . "'";
    
}
    
if (empty($_POST[confirm_password])) {
    
    //if the confirm password field is empty, add the proper value to the message array
    $mess[] = 9;
    
} else {
    
    //if the confirm password field is NOT empty, save the contents of the field
    $mess[confirm_password] = "'" . $_POST[confirm_password] . "'";
    
    //if the new password field is not empty and the confirm password fields does not match
    if (!empty($_POST[new_password]) && $_POST[confirm_password] != $_POST[new_password]) {
            
        //add the proper value to the message array
        $mess[] = 10;
            
    }
}

//if any of the previous message values have been added to the message array
if (isset($mess[0])) {
    
    //redirect the user to the login page and add in all applicable error messages
    header('Location: ' . $base_url . 'login.php/?' . http_build_query($mess));
    exit();
    
}

//if we made it through all of the validation above, then we can submit the user values to the database and add the new user!
//First, lets remove the single quotes around each of the values (the add_user function does not need them the way input fields do)
//NOTE: cannot alter the values of an array from inside a foreach loop? Had to make a new array to get this to work.
foreach($mess as $key => $val) {
    
    $values[$key] = substr($val, 1, -1);
}

//next, call the add_user function from the db_access script and pass in the new $values array, which contains our new user's clean values
add_user($values);

//now that the user has been added to the database we can log them in and redirect them to their profile, all by submitting two hidden fields to the log_user page
//set up the simple form with two hidden inputs and use javascript to automatically submit the form
?>

<form id='hidden_login' name='hidden_login' action='<?=$base_url?>intermediates/log_user.php/?0=0' method='post'>
    <input id='username' name='username' type='hidden' value=<?=$mess[new_username]?>>
    <input id='password' name='password' type='hidden' value=<?=$mess[new_password]?>>
</form>
<script type='text/javascript'>
    document.getElementById('hidden_login').submit();
</script>