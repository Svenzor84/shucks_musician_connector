<?php
/**
* Filename:    my_profile.php
* Author:      Steve Ross-Byers
* Description: Profile page for the currently logged user; access to all user, admin, and cronie functions
*/

session_start();
$_SESSION[page] = "My Profile";
require_once "templates/header.php";

if (!isset($_SESSION[username])) {
    
    //Redirect users who are not logged in back to the home page
    header("Location: " . $base_url);
    exit();
    
}

//we need to grab all instrument, style, and region data from the database and
//we want to reorganize the data in these arrays to make them easier to display
foreach(grab_inst() as $obj) {
    $instruments[$obj->inst_title] = $obj->inst_id;
}

foreach(grab_styles() as $obj) {
    $styles[$obj->style_title][id] = $obj->style_id;
    $styles[$obj->style_title][desc] = $obj->style_desc;
}

foreach(grab_regions() as $obj) {
    $regions[$obj->state_title][$obj->region_title] = $obj->region_id;
}

//if the user was just logged in, display the successful login message
if(isset($_GET['pass'])) {
    
    echo "<h5 class='text-success text-center bg-success message welcome_message'>";
    echo "Your Password Was Successfully Changed To: " . $_GET['pass'] . "<br /> Please Make A Note In Your Records</h5>";
    
} else if(in_array(0, $_GET)) {
        
    echo "<h4 class='text-success text-center bg-success message welcome_message'>";
    echo "Welcome, and thank you for creating a profile!<br />Take a moment to add any instruments and musical styles that you are familiar with, as well as your active region(s) so that other musicians can find and contact you.<br />Now go make some noise!</h4>";
        
} else if(in_array(1, $_GET)) {
    
    echo "<h5 class='text-success text-center bg-success message'>";
    echo "User Successfully Logged In</h5>";
    
} else if(in_array(2, $_GET)) {
    
    echo "<h5 class='text-danger text-center bg-danger message'>";
    echo "Invalid Admin Password</h5>";
    
} else if(in_array(3, $_GET)) {
    
    echo "<h5 class='text-danger text-center bg-danger message'>";
    echo "No Trespassing!</h5>";
    
}else if(in_array(4, $_GET)) {
    
    echo "<h5 class='text-danger text-center bg-danger message'>";
    echo "Invalid Old Password</h5>";
    
}else if(in_array(5, $_GET)) {
    
    echo "<h5 class='text-danger text-center bg-danger message'>";
    echo "Password Confirmation Does Not Match</h5>";
    
}else if(in_array(6, $_GET)) {
    
    echo "<h5 class='text-danger text-center bg-danger message'>";
    echo "New Password Required</h5>";
    
}?>
    
<h2>My Profile</h2>
<div class='col-md-10 col-md-offset-1'>
    
    <div class='col-md-6'>
        <h3>Info</h3>
        <div class='profile_box'>
            <p><?php echo $_SESSION[f_name] . " " . $_SESSION[m_name] . " " . $_SESSION[l_name] . " " . $_SESSION[suffix]?> (aka <?=$_SESSION[username]?>) <button class='btn btn-warning pull-right btn_admin' data-toggle='modal' data-target='#changePass'>Change Password</button></p>
            <h4><u>Member Since</u> <?=date('m/d/y', $_SESSION[signup_date])?>
            
            <?php
            //cronie and admin buttons
            if ($_SESSION[is_cronie] > 0) {
                echo "<button class='btn btn-success pull-right btn_admin' data-toggle='modal' data-target='#croniePortal'>Cronie</button>";
            }
            if ($_SESSION[is_admin] > 0) {
                echo "<button class='btn btn-primary pull-right btn_admin' data-toggle='modal' data-target='#adminPortal'>Admin</button>";
            }?>
            
            </h4>
            
            <h4><u>Email Address</u> <?=$_SESSION[email_address]?> <button class='btn btn-link' data-toggle='modal' data-target='#changeEmail'>Change...</button></h4>
            
            <h4><u>Active Regions</u> <button class='btn btn-link' data-toggle='modal' data-target='#addRegion'>Add...</button></h4>
            <?php if (isset($_SESSION[regions])) {
                
                echo "<div class='row'>";
                
                //nested for loops to access and properly display region data
                foreach($_SESSION[regions] as $state => $num) { ?>
                     
                        <div class='profile_box col-md-4 col-md-offset-1'>
                            <h4><?=$state?></h4>
                            <ul>
                            <?php foreach($num as $region) { ?>
                        
                                <li><?=$region?></li>
                            <?php } ?>
                            </ul>
                        </div>
                    <?php }
                    
                echo "</div>";
                
                } ?>
            
        </div>
    </div>
    
    <div class='col-md-3'>
        <h3>Instruments</h3>
        <div class='profile_box'>
            
            <?php if (isset($_SESSION[inst])) {
                
                foreach($_SESSION[inst] as $inst => $prof){
            
                    echo "<p class='col-md-6'>$inst&nbsp</p>";
                    echo "<p class='col-md-6 text-right'>";
                    for ($i = 0; $i < $prof; $i++) {
                        echo "*";
                    }
                    echo "/10</p>";
                }
            }?>
            
            <button class='btn btn-primary' data-toggle='modal' data-target='#addInst'>Add/Change</button>
        </div>
    </div>
    
    <div class='col-md-3'>
        <h3>Styles</h3>
        <div class='profile_box'>
            
            <?php if (isset($_SESSION[styles])) {
                
                foreach($_SESSION[styles] as $style => $fave){
                
                    if ($fave == 1) {
                        
                        echo "<p class='bg-success text-success'>";
                        
                    } else {
                        
                    echo "<p>";
                    
                    }
                    
                    echo "$style</p>";
                }
            }?>
            
            <button class='btn btn-primary' data-toggle='modal' data-target='#addStyle'>Add</button>
        </div>
    </div>
    
    <div class='col-md-12'>
        <h3>Events</h3>
        <div class='profile_box'>
            <button class='btn btn-primary' data-toggle='modal' data-target='#createEvent'>Create</button>
        </div>
    </div>
    
</div>

<!-- Modals -->
    
    <!-- Admin Portal Popup -->
    <div id='adminPortal' class='modal fade' role='document'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title text-center'>Admin Portal</h4>
                </div>
                <form id='adminPortalForm' name='adminPortalForm' action='<?=$base_url?>intermediates/admin_login.php' method='post' class='form-horizontal'>
                    <div class='modal-body'>
                        <h4 class='text-center'>Please re-enter your Password to Access Admin Features</h4>
                        <div class='form-group'>
                            <div class='col-md-offset-3 col-md-6'>
                                <input type='hidden' id='adminUsername' name='adminUsername' value='<?=$_SESSION[username]?>'>
                                <input type='password' id='adminPassword' name='adminPassword' class='form-control' placeholder='Password'>
                            </div>
                        </div>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-danger col-md-5 pull-right' data-dismiss='modal'><span class='glyphicon glyphicon-remove-sign' aria-hidden='true'></span></button>
                        <button type='submit' class='btn btn-success col-md-5'><span class='glyphicon glyphicon-ok-sign' aria-hidden='true'></span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cronie Portal Popup -->
    <div id='croniePortal' class='modal fade' role='document'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title text-center'>Admin Portal</h4>
                </div>
                <div class='modal-body'>
                    <p>Cronie designation has no function at this point.  Congrats, tho.</p>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-danger' data-dismiss='modal'><span class='glyphicon glyphicon-remove-sign' aria-hidden='true'></span></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Instrument Popup -->
    <div id='addInst' class='modal fade' role='document'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title text-center'>Add/Change Instrument</h4>
                </div>
                <form id='addInstForm' name='addInstForm' action='<?=$base_url?>intermediates/modify_commit.php' method='post'>
                <div class='modal-body'>
                    <div class='row form-group'>
                        <div class='col-md-offset-1 col-md-4'>
                            <select id='instrument' name='instrument' class='form-control text-center'>
                                <option value=''>Select Instrument</option>
                            
                                <?php 
                            
                                foreach($instruments as $key => $value) {
                                    
                                    if (isset($_SESSION[inst][$key])) {
                                        
                                        echo "<option value='update $value'>*$key*</option>";
                                        
                                    } else {
                                        
                                    echo "<option value='$value'>$key</option>";
                                    
                                    }
                                }
                            
                                ?>
                            
                            </select>
                        </div>
                        <div class='col-md-offset-2 col-md-3'>
                            <select id='prof_level' name='prof_level' class='form-control text-center'>
                                <option value=''>Proficiency</option>
                                <?php
                            
                                for ($i = 1; $i < 11; $i++) {
                                
                                    echo "<option value='$i'>$i</option>";
                                }
                            
                                ?>
                            </select>
                        </div>
                        <div class='col-md-2'>
                            <h4>/10</h4>
                        </div>
                    </div>
                    <input type='hidden' id='user_id' name='user_id' value='<?=$_SESSION[user_id]?>'>
                    <input type='hidden' id='function' name='function' value='add_inst'>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-danger col-md-5 pull-right' data-dismiss='modal'><span class='glyphicon glyphicon-remove-sign' aria-hidden='true'></span></button>
                    <button type='submit' class='btn btn-success col-md-5'><span class='glyphicon glyphicon-ok-sign' aria-hidden='true'></span></button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Style Popup -->
    <div id='addStyle' class='modal fade' role='document'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title text-center'>Add Style</h4>
                </div>
                <form id='addStyleForm' name='addStyleForm' action='<?=$base_url?>intermediates/modify_commit.php' method='post'>
                    <div class='modal-body'>
                        <div class='row'>
                            <div class='col-md-offset-1 col-md-10 form-group'>
                                <select id='style' name='style' class='form-control text-center'>
                                    <option value=''>Select Style</option>
                            
                                    <?php
                            
                                    foreach($styles as $key => $value) {
                                
                                        if (isset($_SESSION[styles][$key])) {
                                    
                                            //do nothing if the current style is already in the user's repertoire
                                    
                                        } else {
                                    
                                            echo "<option value='" . $value[id] . "'>$key</option>";
                                        }
                                
                                    }
                            
                                    ?>
                            
                                </select>
                            </div>
                        </div>
                        <input type='hidden' id='user_id' name='user_id' value='<?=$_SESSION[user_id]?>'>
                        <input type='hidden' id='function' name='function' value='add_style'>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-danger col-md-5 pull-right' data-dismiss='modal'><span class='glyphicon glyphicon-remove-sign' aria-hidden='true'></span></button>
                        <button type='submit' class='btn btn-success col-md-5'><span class='glyphicon glyphicon-ok-sign' aria-hidden='true'></span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Region Popup -->
    <div id='addRegion' class='modal fade' role='document'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title text-center'>Add Active Region</h4>
                </div>
                <div class='modal-body'>
                    <?=var_dump($regions)?>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-danger col-md-5 pull-right' data-dismiss='modal'><span class='glyphicon glyphicon-remove-sign' aria-hidden='true'></span></button>
                    <button type='button' class='btn btn-success col-md-5' data-dismiss='modal'><span class='glyphicon glyphicon-ok-sign' aria-hidden='true'></span></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Event Popup -->
    <div id='createEvent' class='modal fade' role='document'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title text-center'>Create New Event</h4>
                </div>
                <div class='modal-body'>
                    <p>This is where the create new Event form goes</p>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-danger col-md-5 pull-right' data-dismiss='modal'><span class='glyphicon glyphicon-remove-sign' aria-hidden='true'></span></button>
                    <button type='button' class='btn btn-success col-md-5' data-dismiss='modal'><span class='glyphicon glyphicon-ok-sign' aria-hidden='true'></span></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Popup -->
    <div id='changePass' class='modal fade' role='document'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title text-center'>Change Password</h4>
                </div>
                <form id='changePassForm' name='changePassForm' action='<?=$base_url?>intermediates/change_password.php' method='post' class='form-horizontal'>
                    <div class='modal-body'>
                        <h4 class='text-center'>Please Enter Your Old Password and Confirm Your New Password</h4>
                        <div class='form-group'>
                            <div class='col-md-offset-3 col-md-6'>
                                <input type='password' id='oldPassword' name='oldPassword' class='form-control' placeholder='Old Password'>
                            </div>
                        </div>
                        <div class='form-group'>
                            <div class='col-md-offset-3 col-md-6'>
                                <input type='password' id='newPassword' name='newPassword' class='form-control' placeholder='New Password'>
                            </div>
                        </div>
                        <div class='form-group'>
                            <div class='col-md-offset-3 col-md-6'>
                                <input type='password' id='confirmPassword' name='confirmPassword' class='form-control' placeholder='Confirm Password'>
                            </div>
                        </div>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-danger col-md-5 pull-right' data-dismiss='modal'><span class='glyphicon glyphicon-remove-sign' aria-hidden='true'></span></button>
                        <button type='submit' class='btn btn-success col-md-5'><span class='glyphicon glyphicon-ok-sign' aria-hidden='true'></span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Email Popup -->
    <div id='changeEmail' class='modal fade' role='document'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title text-center'>Change Email Address</h4>
                </div>
                <div class='modal-body'>
                    <p>This is where the change email address form goes</p>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-danger col-md-5 pull-right' data-dismiss='modal'><span class='glyphicon glyphicon-remove-sign' aria-hidden='true'></span></button>
                    <button type='button' class='btn btn-success col-md-5' data-dismiss='modal'><span class='glyphicon glyphicon-ok-sign' aria-hidden='true'></span></button>
                </div>
            </div>
        </div>
    </div>

<?php require_once "templates/footer.php";