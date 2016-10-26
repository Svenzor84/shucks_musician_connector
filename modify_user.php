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
//$regions = grab_regions($_POST[user_id]);
//$instruments = grab_inst($_POST[user_id]);
//$styles = grab_styles($_POST[user_id]);

$inst = grab_inst($_POST[user_id]);
if (is_array($inst)) {
    foreach($inst as $obj) {
        $instruments[$obj->inst_title][$obj->inst_id] = $obj->prof_level;
    }
}

$sty = grab_styles($_POST[user_id]);
if (is_array($sty)) {
    foreach($sty as $obj) {
        $styles[$obj->style_title][$obj->style_id] = $obj->is_fave;
    }
}

$reg = grab_regions($_POST[user_id]);
if (is_array($reg)) {
    foreach(grab_regions($_POST[user_id]) as $obj) {
        $regions[$obj->state_title][$obj->region_title] = $obj->region_id;
    }
}

//we need to grab all instrument, style, and region data from the database and
//we want to reorganize the data in these arrays to make them easier to display
foreach(grab_inst() as $obj) {
    $all_inst[$obj->inst_title] = $obj->inst_id;
}

foreach(grab_styles() as $obj) {
    $all_styles[$obj->style_title][id] = $obj->style_id;
    $all_styles[$obj->style_title][desc] = $obj->style_desc;
}

foreach(grab_regions() as $obj) {
    $all_regions[$obj->state_title][$obj->region_title] = $obj->region_id;
}


if(in_array(0, $_GET)) {
        
    echo "<h5 class='text-danger text-center bg-danger message'>";
    echo "Form Incomplete<br />All Fields are Required</h5>";
    
} else if (in_array(1, $_GET)) {
    
    echo "<h5 class='text-danger text-center bg-danger message'>";
    echo "Database Error<br />Data Not Committed</h5>";

} else if (in_array(2, $_GET)) {
    
    echo "<h5 class='text-success text-center bg-success message'>";
    echo "Data Updated Successfully</h5>";

} else if (in_array(3, $_GET)) {
    
    echo "<h5 class='text-danger text-center bg-danger message'>";
    echo "Invalid Data Entry - All Fields Must Adhere to Size and Pattern Requirements<br />
          Username: 6-30 Characters, Minimum of One Letter (Upper or Lower Case), Other Valid Characters: 0-9 . _<br />
          Other Names: 1-30 Characters, Minimum of One Letter (Upper or Lower Case), Other Valid Characters: - ' [space]</h5>";

}else if (in_array(4, $_GET)) {
    
    echo "<h5 class='text-danger text-center bg-danger message'>";
    echo "Invalid Data Entry - Username Must Be Unique (Requested Username is Already in Use)</h5>";

}?>

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
                    <input type='hidden' name='old_username' id='old_username' value='<?=$_POST[username]?>'>
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
                    <input type='hidden' name='old_f_name' id='old_f_name' value='<?=$_POST[f_name]?>'>
                </div>
            </div>
            
            <div class='row form-group'>
                <div class='col-md-4'>
                    <!-- <h5 class='text-right'><?php echo empty($_POST[m_name]) ? "<em>None</em>" : $_POST[m_name]?></h5> -->
                    <h5 class='text-right'><?php echo empty($_POST[m_name]) ? "-" : $_POST[m_name]?></h5>
                </div>
                <div class='col-md-2'>
                    <h5 class='text-center'>to</h5>
                </div>
                <div class='col-md-4'>
                    <input class='form-control' type='text' name='m_name' id='m_name' placeholder='Middle Name (Optional)' value='<?=$_POST[m_name]?>'>
                    <input type='hidden' name='old_m_name' id='old_m_name' value='<?=$_POST[m_name]?>'>
                </div>
                <div class='col-md-1 checkbox'>
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
                    <input type='hidden' name='old_l_name' id='old_l_name' value='<?=$_POST[l_name]?>'>
                </div>
            </div>
            <div class='row form-group'>
                <!-- <h5 class='col-md-4 text-right'><?php echo empty($_POST[suffix]) ? "<em>None</em>" : $_POST[suffix]?></h5> -->
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
                <input type='hidden' name='old_suffix' id='old_suffix' value='<?=$_POST[suffix]?>'>
                <div class='col-md-1 checkbox'>
                </div>
            </div>
            <div class='row'>
                <input type='hidden' name='func' id='func' value='update_info'>
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
                echo "<input type='hidden' name='func' id='func' value='demote'>";
                echo "<input type='hidden' name='user_id' id='user_id' value='" . $_POST[user_id] . "'>";
                echo "<button type='submit' class='btn btn-danger'>From Admin</button>";
                echo "</form>";
                
            } else {
                
                echo "<h4>Promote this User?</h4>";
                echo "<form name='promote' id='promote' action='" . $base_url . "intermediates/modify_commit.php' method='post'>";
                echo "<input type='hidden' name='is_cronie' id='is_cronie' value='" . $_POST[is_cronie] . "'>";
                echo "<input type='hidden' name='is_admin' id='is_admin' value='" . $_POST[is_admin] . "'>";
                echo "<input type='hidden' name='func' id='func' value='promote'>";
                echo "<input type='hidden' name='user_id' id='user_id' value='" . $_POST[user_id] . "'>";
                
                if ($_POST[is_cronie] == 1) {
                    
                    echo "<button type='submit' class='btn btn-success'>To Admin</button>";
                    echo "</form>";
                    echo "<h4>Demote this User?</h4>";
                    echo "<form name='demote' id='demote' action='" . $base_url . "intermediates/modify_commit.php' method='post'>";
                    echo "<input type='hidden' name='is_cronie' id='is_cronie' value='" . $_POST[is_cronie] . "'>";
                    echo "<input type='hidden' name='is_admin' id='is_admin' value='" . $_POST[is_admin] . "'>";
                    echo "<input type='hidden' name='func' id='func' value='demote'>";
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
    <div class='row col-md-12'>
    <div class='col-md-4'>
        <h3>Instruments</h3>
        <div class='profile_box'>
            <?php
            
            
            if(is_array($instruments)) {
                foreach($instruments as $title => $array) {
                    foreach($array as $id => $prof_level)
                
                    echo "<div class='row'>
                        <form name='change_inst' id='change_inst' action='" . $base_url . "intermediates/modify_commit.php' method='post'>
                            <div class='col-md-4'>
                                <h5>" . $title . "</h5>
                            </div>
                            <div class='col-md-3'>
                                <select id='prof_level' name='prof_level' class='form-control text-center'>
                                    <option value=''>Proficiency</option>";
                    for ($i = 1; $i < 11; $i++) {
                                
                        if ($i == $prof_level) {
                            echo "<option value='$i' class='text-danger' selected>*$i*</option>";
                        } else {
                            echo "<option value='$i'>$i</option>";
                        }
                    }
                    echo "      </select>
                            </div>
                            <div class='col-md-3 checkbox'>
                                <label><input type='checkbox' id='remove' name='remove' value='yes'>Remove</label>
                            </div>
                            <div class='col-md-2'>
                                <button type='submit' class='btn btn-warning pull-right'>GO</button>
                            </div>
                            <input type='hidden' id='user_id' name='user_id' value='" . $_POST[user_id] . "'>
                            <input type='hidden' id='old_prof_level' name='old_prof_level' value='" . $prof_level . "'>
                            <input type='hidden' id='inst_id' name='inst_id' value='" . $id . "'>
                            <input type='hidden' id='func' name='func' value='change_inst'>
                        </form>
                      </div>";
                
                }
                
            } else {
            
                echo "<h4 class='text-center'><em>None</em></h4>";
            }?>
            
            <div class='row'>
                <div class='col-md-12'>
                    <button class='btn btn-primary pull-right' data-toggle='modal' data-target='#addInst'>Add</button>
                </div>
            </div>
        </div>
    </div>
    <div class='col-md-4'>
        <h3>Styles</h3>
        <div class='profile_box'>
            <?php
            
            if(is_array($styles)) {
                foreach($styles as $title => $array) {
                    foreach($array as $id => $is_fave) {?>
                    
                        <div class='row'>
                            <form name='change_style' id='change_style' action='<?=$base_url?>intermediates/modify_commit.php' method='post'>
                                <div class='col-md-4'>
                                    <?php echo $is_fave == 1 ? "<h4 class='bg-success text-success'>" : "<h4>"?>
                                    <?=$title?></h4>
                                </div>
                                <div class='col-md-5 radio'>
                                    <label><input type='radio' id='removeOrFave' name='removeOrFave' value='Fave' checked>Fave</label>
                                    <label class='pull-right'><input type='radio' id='removeOrFave' name='removeOrFave' value='Remove' class='pull-right'>Remove</label>
                                </div>
                                <div class='col-md-3'>
                                    <button type='submit' class='btn btn-warning pull-right'>GO</button>
                                </div>
                                <input type='hidden' id='user_id' name='user_id' value='<?=$_POST[user_id]?>'>
                                <input type='hidden' id='style_id' name='style_id' value='<?=$id?>'>
                                <input type='hidden' id='func' name='func' value='change_style'>
                            </form>
                        </div>
                    
                <?php }
                }
                
            } else {
            
                echo "<h4 class='text-center'><em>None</em></h4>";
            }?>
            <div class='row'>
                <div class='col-md-12'>
                    <button class='btn btn-primary pull-right' data-toggle='modal' data-target='#addStyle'>Add</button>
                </div>
            </div>
        </div>
    </div>
    <div class='col-md-4'>
        <h3>Active Regions</h3>
        <div class='profile_box'>

            <?php

            if (is_array($regions)) {
                
                echo "<div class='row'>
                        <ul id='states'>";
                foreach($regions as $state => $array) {
                                        
                    echo "<li><h4>$state</h4></li>";
                                        
                    foreach ($array as $region => $id) {

                        echo "<div class='row'>
                            <form id='remove_region' name='remove_region' action='" . $base_url . "intermediates/modify_commit.php' method='post'>
                                <div class='col-md-offset-1 col-md-5'>
                                    <h5>$region</h5>
                                </div>
                                <div class='col-md-5'>
                                    <button type='submit' class='btn btn-danger pull-right'>Remove</button>
                                </div>
                                <input type='hidden' id='user_id' name='user_id' value='" . $_POST[user_id] . "'>
                                <input type='hidden' id='region_id' name='region_id' value='$id'>
                                <input type='hidden' id='func' name='func' value='remove_region'>
                            </form>
                        </div>";
                    }
                }
                echo "</ul>
                    </div>";
            } else {
            
                echo "<h4 class='text-center'><em>None</em></h4>";
            }?>

            <div class='row'>
                <div class='col-md-12'>
                    <button class='btn btn-primary pull-right' data-toggle='modal' data-target='#addRegion'>Add</button>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

<!-- Modals -->
<!-- Add Instrument Popup -->
    <div id='addInst' class='modal fade' role='document'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title text-center'>Add Instrument</h4>
                </div>
                <form id='addInstForm' name='addInstForm' action='<?=$base_url?>intermediates/modify_commit.php' method='post'>
                <div class='modal-body'>
                    <div class='row form-group'>
                        <div class='col-md-offset-1 col-md-4'>
                            <select id='inst_id' name='inst_id' class='form-control text-center'>
                                <option value=''>Select Instrument</option>
                                
                                <?php
                                
                                foreach($all_inst as $title => $id) {

                                            if (isset($instruments[$title])) {
                                                
                                            } else {
                                                echo "<option value='" . $id . "'>" . $title . "</option>";
                                            }
                                }?>
                                
                            </select>
                        </div>
                        <div class='col-md-3'>
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
                    <input type='hidden' id='user_id' name='user_id' value='<?=$_POST[user_id]?>'>
                    <input type='hidden' id='func' name='func' value='add_inst'>
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
                        <div class='row form-group'>
                            <div class='col-md-offset-1 col-md-7'>
                                <select id='style_id' name='style_id' class='form-control text-center'>
                                    <option value=''>Select Style</option>
                                    
                                    <?php
                            
                                    foreach($all_styles as $style => $value) {
                                
                                        if (isset($styles[$style])) {
                                    
                                            //do nothing if the current style is already in the user's repertoire
                                    
                                        } else {
                                    
                                            echo "<option value='" . $value[id] . "'>$style</option>";
                                        }
                                
                                    }?>
                                    
                                </select>
                            </div>
                            <div class='col-md-4 checkbox'>
                                <label><input type='checkbox' id='is_fave' name='is_fave' value='1'>Make Fave</label>
                            </div>
                        </div>
                        <input type='hidden' id='user_id' name='user_id' value='<?=$_POST[user_id]?>'>
                        <input type='hidden' id='func' name='func' value='add_style'>
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
                <form id='addRegionForm' name='addRegionForm' action='<?=$base_url?>intermediates/modify_commit.php' method='post'>
                    <div class='modal-body'>
                        <div class='row form-group'>
                            <div class='col-md-offset-1 col-md-10'>
                                <select id='region_id' name='region_id' class='form-control text-center'>
                                    <option value=''>Select Region</option>
                                    <?php 
                                    
                                    foreach($all_regions as $state => $array) {
                                        
                                        echo "<optgroup class='text-left' label='$state'>";
                                        
                                        foreach ($array as $region => $id) {
                                            
                                            if (isset($regions[$state][$region])) {
                                                
                                                //do nothing if the region has already been added to the user's profile
                                                
                                            } else {
                                            
                                                echo "<option class='' value='$id'>$region</option>";
                                            }
                                        }
                                        
                                        echo "</optgroup>";
                                    }?>
                                </select>
                            </div>
                        </div>
                        <input type='hidden' id='user_id' name='user_id' value='<?=$_POST[user_id]?>'>
                        <input type='hidden' id='func' name='func' value='add_region'>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-danger col-md-5 pull-right' data-dismiss='modal'><span class='glyphicon glyphicon-remove-sign' aria-hidden='true'></span></button>
                        <button type='submit' class='btn btn-success col-md-5'><span class='glyphicon glyphicon-ok-sign' aria-hidden='true'></span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php require_once "templates/footer.php";