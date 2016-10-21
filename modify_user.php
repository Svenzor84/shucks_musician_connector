<?php
/**
* Filename:    modify_user.php
* Author:      Steve Ross-Byers
* Description: User modification page, displays all data for a given user, Admin Function
*/

session_start();

//if the proper post variables were not set
if (!isset($_POST[username])) {
    
    //redirect the user with a tresspassing message
    header("Location: " . $base_url . "my_profile.php/?0=3");
    exit();
}

$_SESSION[page] = "Modify " . $_POST[username];
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

//we have ensured the user is supposed to be here, so let us grab some extra user data
$regions = grab_regions($_POST[user_id]);
$instruments = grab_inst($_POST[user_id]);
$styles = grab_styles($_POST[user_id]);
?>

<h2>Modify User - <?=$_POST[user_id]?> - <?=$_POST[email_address]?> - Joined <?=$_POST[signup_date]?></h2>
<div class='col-md-10 col-md-offset-1'>
    <div class='col-md-7'>
        <h3>Info</h3>
        <div class='profile_box'>
            <form id='info' name='info' action='<?=$base_url?>/intermediates/modify_commit.php' method='post'>
                
            <div class='row form-group'>
                <div class='col-md-4'>
                    <h5 class='text-right'><?=$_POST[username]?></h5>
                </div>
                <div class='col-md-2'>
                    <h5 class='text-center'>to</h5>
                </div>
                <div class='col-md-4'>
                    <input class='form-control' type='text' name='username' id='username' placeholder='Username' value='<?=$_POST[username]?>'>
                </div>
            </div>
            
            <div class='row form-group'>
                <div class='col-md-4'>
                    <h5 class='text-right'><?=$_POST[f_name]?></h5>
                </div>
                <div class='col-md-2'>
                    <h5 class='text-center'>to</h5>
                </div>
                <div class='col-md-4'>
                    <input class='form-control' type='text' name='f_name' id='f_name' placeholder='First Name' value='<?=$_POST[f_name]?>'>
                </div>
            </div>
            
            <div class='row form-group'>
                <div class='col-md-4'>
                    <h5 class='text-right'><?php echo empty($_POST[m_name]) ? "<em>None</em>" : $_POST[m_name]?></h5>
                </div>
                <div class='col-md-2'>
                    <h5 class='text-center'>to</h5>
                </div>
                <div class='col-md-4'>
                    <input class='form-control' type='text' name='m_name' id='m_name' placeholder='Middle Name (Optional)' value='<?=$_POST[m_name]?>'>
                </div>
                <div class='col-md-1 checkbox'>
                    <label><input class='' type='checkbox' name='clear_mid' id='clear_mid' value='yes'>clear</label>
                </div>
            </div>
            
            
            <div class='row form-group'>
                <div class='col-md-4'>
                    <h5 class='text-right'><?=$_POST[l_name]?></h5>
                </div>
                <div class='col-md-2'>
                    <h5 class='text-center'>to</h5>
                </div>
                <div class='col-md-4'>
                    <input class='form-control' type='text' name='l_name' id='l_name' placeholder='Last Name' value='<?=$_POST[l_name]?>'>
                </div>
            </div>
            <div class='row form-group'>
                <h5 class='col-md-4 text-right'><?php echo empty($_POST[suffix]) ? "-" : $_POST[suffix]?></h5>
                <h5 class='col-md-2 text-center'>to</h5>
                <div class='col-md-4'>
                    <select id='suffix' name='suffix' class='text-center form-control'>
                        <option value=''>-</option>
                        <option value='Jr.' <?php echo ("'" . $_POST[suffix] . "'" == '\'Jr.\'') ? 'selected' : '' ?> >Jr.</option>
                        <option value='Sr.' <?php echo ("'" . $_POST[suffix] . "'" == '\'Sr.\'') ? 'selected' : '' ?> >Sr.</option>
                        <option value='I' <?php echo ("'" . $_POST[suffix] . "'" == '\'I\'') ? 'selected' : '' ?> >I</option>
                        <option value='II' <?php echo ("'" . $_POST[suffix] . "'" == '\'II\'') ? 'selected' : '' ?> >II</option>
                        <option value='III' <?php echo ("'" . $_POST[suffix] . "'" == '\'III\'') ? 'selected' : '' ?> >III</option>
                        <option value='IV' <?php echo ("'" . $_POST[suffix] . "'" == '\'IV\'') ? 'selected' : '' ?> >IV</option>
                        <option value='V' <?php echo ("'" . $_POST[suffix] . "'" == '\'V\'') ? 'selected' : '' ?> >V</option>
                        <option value='VI' <?php echo ("'" . $_POST[suffix] . "'" == '\'VI\'') ? 'selected' : '' ?> >VI</option>
                        <option value='VII' <?php echo ("'" . $_POST[suffix] . "'" == '\'VII\'') ? 'selected' : '' ?> >VII</option>
                    </select>
                </div>
                <div class='col-md-1 checkbox'>
                    <label><input type='checkbox' name='clear_suff' id='clear_suff' value='yes'>clear</label>
                </div>
            </div>
            <div class='row'>
                <input type='hidden' name='function' id='function' value='update_info'>
                <input type='hidden' name='user_id' id='user_id' value='<?=$_POST[user_id]?>'>
                <button type='submit' class='btn btn-success col-md-offset-2 col-md-8'>Commit</button>
            </div>
            </form>
        </div>
    </div>
    <div class='col-md-5'>
        <h3>Promotions</h3>
        <div class='profile_box text-center'>
            <?php if ($_POST[is_admin] == 1) {
                
                echo "<h4>This User is an Admin</h4>";
                echo "<h4>Demote this User?</h4>";
                echo "<form name='demote' id='demote' action='" . $base_url . "intermediates/modify_commit.php' method='post'>";
                echo "<input type='hidden' name='is_cronie' id='is_cronie' value='" . $_POST[is_cronie] . "'>";
                echo "<input type='hidden' name='is_admin' id='is_admin' value='" . $_POST[is_admin] . "'>";
                echo "<input type='hidden' name='function' id='function' value='demote'>";
                echo "<input type='hidden' name='user_id' id='user_id' value='" . $_POST[user_id] . "'>";
                echo "<button type='submit' class='btn btn-danger'>From Admin</button>";
                echo "</form>";
                
            } else {
                
                echo "<h4>Promote this User?</h4>";
                echo "<form name='promote' id='promote' action='" . $base_url . "intermediates/modify_commit.php' method='post'>";
                echo "<input type='hidden' name='is_cronie' id='is_cronie' value='" . $_POST[is_cronie] . "'>";
                echo "<input type='hidden' name='is_admin' id='is_admin' value='" . $_POST[is_admin] . "'>";
                echo "<input type='hidden' name='function' id='function' value='promote'>";
                echo "<input type='hidden' name='user_id' id='user_id' value='" . $_POST[user_id] . "'>";
                
                if ($_POST[is_cronie == 1]) {
                    
                    echo "<button type='submit' class='btn btn-success'>To Admin</button>";
                    echo "</form>";
                    echo "<h4>Demote this User?</h4>";
                    echo "<form name='demote' id='demote' action='" . $base_url . "intermediates/modify_commit.php' method='post'>";
                    echo "<input type='hidden' name='is_cronie' id='is_cronie' value='" . $_POST[is_cronie] . "'>";
                    echo "<input type='hidden' name='is_admin' id='is_admin' value='" . $_POST[is_admin] . "'>";
                    echo "<input type='hidden' name='function' id='function' value='demote'>";
                    echo "<input type='hidden' name='user_id' id='user_id' value='" . $_POST[user_id] . "'>";
                    echo "<button type='submit' class='btn btn-danger'>From Cronie</button>";
                    echo "</form>";
                    
                } else {
                    
                    echo "<button type='submit' class='btn btn-primary'>To Cronie</button>";
                    echo "</form>";
                }
                
            }
            
            
            ?>
        </div>
    </div>
    <div class='col-md-12'>
        <h3>Instruments</h3>
        <div class='profile_box'>
            <?=var_dump($instruments)?>
        </div>
    </div>
    <div class='col-md-6'>
        <h3>Styles</h3>
        <div class='profile_box'>
            <?=var_dump($styles)?>
        </div>
    </div>
    <div class='col-md-6'>
        <h3>Regions</h3>
        <div class='profile_box'>
            <?=var_dump($regions)?>
        </div>
    </div>
</div>

<?php require_once "templates/footer.php";