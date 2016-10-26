<?php
/**
* Filename:    modify_commit.php
* Author:      Steve Ross-Byers
* Description: Intermediate database update file, allows users (and admins)
*              to alter their data in the database. Handles ALL database updates 
*               (other than remove_user.php and change_password.php...why?)
*/

require_once('db_access.php');
session_start();

//set up regular expressions for checking validity of user entries
$username_pattern = "/^(?![_.])(?!.*[_.]{2})(?=.*[A-Za-z])[\w.]+(?<![_.])$/";
$name_pattern = "/^(?![- ])(?!.*[- ]{2})(?!.*[']{2})(?=.*[A-Za-z])[A-Za-z- ']+(?<![- ])$/";

//PASIVE USER VALIDATION
if (!isset($_SESSION[username])) {
    
    //Redirect users who are not logged in back to the home page
    header("Location: " . $base_url);
    exit();
    

}
//if the POST variable is empty, redirect the user to the shucks! home page
if (empty($_POST)) {
    
    //add the proper value to the message array
    $mess[] = 3;
    
    //redirect the user to their profile page
    header('Location: ' . $base_url . 'my_profile.php/?' . http_build_query($mess));
    exit();
    
}

//if the user is trying to alter a different user's data (and the current user is not an admin)
if (!empty($_POST[username]) && ($_POST[username] != $_SESSION[username]) && $_SESSION[is_admin] != 1) {
    
    //add the proper value to the message array
    $mess[] = 3;
    
    //redirect the user to their profile page
    header('Location: ' . $base_url . 'my_profile.php/?' . http_build_query($mess));
    exit();
}

//final validation is a catch-all for empty form fields
//users are redirected based on their access point, user or admin
foreach($_POST as $key => $value) {
    
    //if any of the post variables are empty
    if (empty($value) && $value != '0') {
        
        if($key != 'm_name' && $key != 'old_m_name' && $key != 'suffix' && $key != 'old_suffix' && $key != 'prof_level') {
        
            redirect_user(0);
        }
    }
}

//now that we have passed validation we can switch on the func of our submitted form and decide which function to call
switch ($_POST[func]) {
    
    case 'add_inst':
        
        //call the add instrument function (from the db_access script) and redirect on success or failure
        if (add_instrument($_POST[user_id], $_POST[inst_id], $_POST[prof_level])) {
            
            redirect_user();
        } else {
            
            redirect_user(1);
        };
        break;
    
    case 'change_inst':
        
        if (($_POST[old_prof_level] == $_POST[prof_level]) && $_POST[remove] != 'yes') {
            
            redirect_user(1);
        }
        
        //call the change instrument function (from the db_access script) and redirect on success or failure
        if (change_instrument($_POST[user_id], $_POST[inst_id], $_POST[prof_level], $_POST[remove])) {
            
            redirect_user();

        } else {
            
            redirect_user(1);
        };
        break;
     
    case 'add_style':
        
        //call the add style function and redirect the user accordingly
        if (add_style($_POST[user_id], $_POST[style_id], $_POST[is_fave])) {
            
            redirect_user();
            
        } else {
            
            redirect_user(1);
        }
        break;
     
    case 'change_style':

        //call the add style function and redirect the user accordingly
        if (change_style($_POST[user_id], $_POST[style_id], $_POST[removeOrFave])) {
            
            redirect_user();
            
        } else {
            
            redirect_user(1);
        }
        break;
        
    case 'add_region':
        
        if (add_region($_POST[user_id], $_POST[region_id])) {
            
            
            redirect_user();
            
        } else {
            
            redirect_user(1);
        }
        
        break;
     
    case 'remove_region':
        
        if (remove_region($_POST[user_id], $_POST[region_id])) {
            
            redirect_user();
            
        } else {
            
            redirect_user(1);
        }
        
        break;
    
     case 'update_info':
        
        if (($_POST[username] != $_POST[old_username]) && check_username($_POST[username])) {
            
            redirect_user(4);
            
        } else if (strlen($_POST[username]) < 6 || strlen($_POST[username]) > 30 || !preg_match($username_pattern, $_POST[username]) || 
                   strlen($_POST[f_name]) < 1 || strlen($_POST[f_name]) > 30 || !preg_match($name_pattern, $_POST[f_name]) ||
                   strlen($_POST[m_name]) > 30 || (!empty($_POST[m_name]) && !preg_match($name_pattern, $_POST[m_name])) ||
                   strlen($_POST[l_name]) < 1 || strlen($_POST[l_name]) > 30 || !preg_match($name_pattern, $_POST[l_name])) {
            
            redirect_user(3);
            
        } else if (update_user($_POST)) {
            
            redirect_user();
            
        } else {
            
           redirect_user(1);
        }
        
        break;
    
    case 'promote':
        
        if (promote_user($_POST[user_id], $_POST[is_cronie])) {
            
            redirect_user();
            
        } else {
            
            redirect_user(1);
        }
        break;
    
    case 'demote':
        echo "Demotion :(</h1>";
        var_dump($_POST);
        
        if (demote_user($_POST[user_id], $_POST[is_cronie])) {
            
            redirect_user();
            
        } else {
            
            redirect_user(1);
        }
        break;

    
    default:
        echo "Modify Commit Error...</h1>";
        var_dump($_POST);
        break;
}

//simple helper function redirects users depending on their access point (admin or user) [default message code 2 is for success]
function redirect_user($message_code = 2) {
    
    global $base_url;
    
    //admins are sent to the modify user page (will require building a hidden field and re-submitting the post data)
    if ($_POST[user_id] != $_SESSION[user_id]) {
        
        echo "<form id='returnData' name='returnData' action='" . $base_url . "modify_user.php/?0=$message_code";
        echo "' method='post'>";
        $user_data = grab_user($_POST[user_id]);
        foreach($user_data as $key => $value) {
            
            //create a hidden input containing all of the post values to be submitted back to the modify user page
            echo "<input type='hidden' name='$key' id='$key' value='$value'>";
        }
        echo "</form>";?>
        <script type='text/javascript'>
            document.getElementById('returnData').submit();
        </script>
        <?php
    //users are returned to their profile page with a message
    } else {
            
        switch($message_code) {
                
            case '0':
                $mess[] = 7;
                break;
                    
            case '1':
                $mess[] = 8;
                break;
                
            case '3':
                $mess[] = 10;
                break;
                
            case '4':
                $mess[] = 11;
                break;    
            
            default:

                //default means success, so we want to refresh the user's data
                //first we unset the proper SESSION values/arrays
                unset($_SESSION[f_name]);
                unset($_SESSION[m_name]);
                unset($_SESSION[l_name]);
                unset($_SESSION[suffix]);
                unset($_SESSION[username]);
                unset($_SESSION[is_admin]);
                unset($_SESSION[is_cronie]);
                unset($_SESSION[styles]);
                unset($_SESSION[regions]);
                unset($_SESSION[inst]);

                refresh_data($_SESSION[user_id]);
                $mess[] = 9;
                break;
        }
        
        //redirect the user to their profile page
        header('Location:' . $base_url . 'my_profile.php/?' . http_build_query($mess));
        exit();
    }
}