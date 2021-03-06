<?php
/**
* Filename:    admin_func.php
* Author:      Steve Ross-Byers
* Description: Admin function page, gives admin access to user data
*/

session_start();
$_SESSION[page] = "Admin Functions";
require_once "templates/header.php";

if (!isset($_SESSION[username])) {
    
    //Redirect users who are not logged in back to the home page
    header("Location: " . $base_url);
    exit();
    
} else if ($_SESSION[is_admin] != 1) {
    
    //add the proper value to the message array
    $mess[] = 3;
    
    //redirect the user to their profile page
    header('Location: ' . $base_url . 'my_profile.php/?' . http_build_query($mess));
    exit();
}

//if the post variable is not empty and the form was submitted via the clear search button, or the search field contains only spaces
if (!empty($_POST) && ($_POST[submit] == 'clear' || ctype_space($_POST[searchField]))) {
    
    //clear the search field
    $_POST[searchField] = '';
}

//if the POST variable is not empty, and the search field is not empty, we have some work to do
if (!empty($_POST) && $_POST[searchField] != '') {

    //first, we need to explode the search field into an array
    $query = explode(' ', $_POST[searchField]);

    //call the grab users function, pass in the search query as an exploded array, and save the result in the users variable
    $users = grab_users($query);

//if POST has not been set, or the search field is empty
} else {

    //call the grab users function with no parameters and save the result in the users variable
    $users = grab_users();
}

if(in_array(0, $_GET)) {
        
    echo "<h4 class='text-warning text-center bg-warning message'>";
    echo "Admin Reset Password Function Is Currently Disabled</h4>";
        
} else if (in_array(1, $_GET)) {

    echo "<h5 class='text-success text-center bg-success message'>";
    echo "User Successfully Removed</h5>";
    
} else if (in_array(2, $_GET)) {

    echo "<h5 class='text-danger text-center bg-danger message'>";
    echo "User Removal Failed</h5>";
}?>

<h2>Administrator Functions</h2>
<div class='col-md-12'>
    <h3>Search</h3>
    <div class='profile_box'>
        <form id='adminSearch' name='adminSearch' method='post' action='<?=$base_url?>admin_func.php' class='form-horizontal'>
            <div class='form-group'>
                <div class='col-md-10'>
                    <div class='input-group'>
                        <input type='text' id='searchField' name='searchField' class='form-control' placeholder='Search by Username, User ID, Email Address, First, Middle, or Last Name' value="<?php echo isset($_POST[searchField]) ? htmlspecialchars($_POST[searchField]) : '' ?>">
                        <div class='input-group-btn'>
                            <button type='submit' name='submit' class='btn btn-primary' value='search'>Search</button>
                        </div>
                    </div>
                </div>
                <button type='submit' name='submit' class='btn btn-danger' value='clear'>Clear Search</button>
            </div>
        </form>
    </div>
</div>

<div class='col-md-12'>
    <h3>Users <?php echo !empty($_POST[searchField]) ? '(' . $_POST[searchField] . ')' : '' ?> [<?=count($users)?>]</h3>
    <div class='profile_box'>
        <?php
        //if the search came up empty
        if ($users == false) {
            
            //let the user know
            echo "<h3 class='text-danger'>Nothing Found</h3>";
            
        //otherwise, display the results
        } else { ?>
        <table class='table table-hover'>
            <thead class=''>
                <tr>
                    <?php
                    
                    foreach ($users[0] as $column => $value) {
                        
                        if($column == 'password') {
                            
                            echo "<th>" . $column . "</th>";
                            
                        } else {
                            
                            echo "<th><a href='" . $base_url . "admin_func.php/?sort=$column";
                            
                            if ($_GET['sort'] == $column) {
                                
                                echo "_inv";
                            }
                            
                            echo "'>" . $column . "</a></th>";
                        }
                    }
                    
                    ?>
                    <th>
                        modify
                    </th>
                    <th>
                        remove
                    </th>
                </tr>
            </thead>
            <tbody>
                
                <?php
                
                foreach ($users as $obj) {
                    
                    echo "<tr>";
                    //we need to create a hidden form for each user for passing that user's information into the modify user page
                    echo "<form id='modify_" . $obj->user_id . "' name='modify_" . $obj->user_id . "' action='" . $base_url . "modify_user.php' method='post'>";
                    
                    foreach($obj as $column => $value) {
                        
                        if($column != 'password') {
                            
                            echo "<td>$value</td>";
                            //add a hidden input for each piece of data
                            echo "<input type='hidden' id='$column' name='$column' value='$value'>";
                            
                        } else {
                            
                            echo "<td><a href='" . $base_url . "intermediates/change_password.php/?user=$obj->user_id' class='btn btn-warning'>Reset</a></td>";
                        }
                    }
                    
                    echo "<td><button type='submit' class='btn btn-primary'>Modify</button></td>";
                    echo "<td><a href='" . $base_url . "intermediates/remove_user.php/?user=$obj->user_id' class='btn btn-danger'>Remove</a></td>";
                    echo "</form>";
                    echo "</tr>";
                }
                
                ?>
                
            </tbody>
        </table>
        <?php } ?>
    </div>
</div>

<?php require_once "templates/footer.php";