<?php
/**
* Filename:    login.php
* Author:      Steve Ross-Byers
* Description: Login portal for Shucks! website; allows users to enter their information and begin a logged session
*/

session_start();
$_SESSION[page] = "Login";
require_once "templates/header.php";

if (isset($_SESSION[username])) {
    
    //Redirect users who are already logged in to the home page
    header("Location: " . $base_url);
    exit();
    
}?>

    <div class='col-md-10 col-md-offset-1'>

        <!-- Login Form -->
        <div class='col-md-4'>
            <h3 class='text-center'>Login</h3>
            <form id='login' name='login' action='<?=$base_url?>intermediates/log_user.php' method='post' class='form-horizontal'>
                <div class="form-group <?php echo (in_array(2, $_GET) || in_array(1, $_GET)) ? 'has-error' : (isset($_GET[username]) ? 'has-success' : '') ?>">
                    <div>
                    <?php if (in_array(2, $_GET) || in_array(1, $_GET)){
                        echo "<h5 class='text-danger text-center bg-danger message'>";
                        if (in_array(2, $_GET)) {
                            echo "Username or Email Address Required";
                        }
                        if (in_array(1, $_GET)) {
                            echo "Incorrect Username or Password";
                        }
                        echo "</h5>";
                    }?>
                        <input type='text' id='username' name='username' class='form-control' placeholder='Username or Email Address' value="<?php echo isset($_GET[username]) ? htmlspecialchars($_GET[username]) : '' ?>">
                    </div>
                </div>
                <div class="form-group <?php echo (in_array(3, $_GET) || in_array(1, $_GET)) ? 'has-error' : (isset($_GET[password]) ? 'has-success' : '') ?>">
                    <div>
                    <?php if (in_array(3, $_GET)){
                        echo "<h5 class='text-danger text-center bg-danger message'>";
                        echo "Password Required";
                        echo "</h5>";
                    }?>
                        <input type='password' id='password' name='password' class='form-control' placeholder='Password' value="<?php echo isset($_GET[password]) ? htmlspecialchars($_GET[password]) : '' ?>">
                    </div>
                </div>
                <div class='form-group'>
                    <div class='col-md-offset-2 col-md-8'>
                        <input type='submit' value='Login' class='form-control btn-default'>
                    </div>
                </div>
            </form>
        </div>

        <!-- Create New Account Form -->
        <div class='col-md-8'>
            <h3 class='text-center'>Create Account</h3>
            <form id='create' name='create' action='<?=$base_url?>intermediates/create_user.php' method='post' class='form-horizontal'>
                
                <!-- Form Group 1 (Username and Email)-->
                <div class="form-group">
                    <div class="col-md-5 col-md-offset-1 <?php echo (in_array(4, $_GET) || in_array(11, $_GET) || in_array(13, $_GET)) ? 'has-error' : (isset($_GET[new_username]) ? 'has-success' : '') ?>">
                        <div>
                            <?php
                                if (in_array(4, $_GET) || in_array(5, $_GET) || in_array(11, $_GET) || in_array(12, $_GET) || in_array(13, $_GET)) {
                                echo "<h5 class=";
                                echo (in_array(4, $_GET)) ? "'text-danger text-center bg-danger message'>Username Required" : (in_array(11, $_GET) ? "'text-danger text-center bg-danger message'>Invalid Username" : (in_array(13, $_GET) ? "'text-danger text-center bg-danger message'>Username is Not Unique" : "'message'>&nbsp"));
                                echo "</h5>";
                            }?>

                            <!-- Username Input with Help Button -->
                            <div class='input-group'>
                                <input type='text' id='new_username' name='new_username' class='form-control' placeholder='Username' value="<?php echo isset($_GET[new_username]) ? htmlspecialchars(substr($_GET[new_username], 1, -1)) : '' ?>">
                                <a data-toggle='modal' data-target='#usernameHelp' class='btn btn-default input-group-addon'>&nbsp?&nbsp</a>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-5 <?php echo (in_array(5, $_GET) || in_array(12, $_GET)) ? 'has-error' : (isset($_GET[new_email]) ? 'has-success' : '') ?>">
                        <div>
                        <?php
                        if (in_array(5, $_GET) || in_array(4, $_GET) || in_array(12, $_GET) || in_array(11, $_GET) || in_array(13, $_GET)) {
                            echo "<h5 class=";
                            echo (in_array(5, $_GET)) ? "'text-danger text-center bg-danger message'>Email Address Required" : (in_array(12, $_GET) ? "'text-danger text-center bg-danger message'>Invalid Email Address" : "'message'>&nbsp");
                            echo "</h5>";
                        }?>
                            <input type='text' id='new_email' name='new_email' class='form-control' placeholder='Email Address' value="<?php echo isset($_GET[new_email]) ? htmlspecialchars(substr($_GET[new_email], 1, -1)) : '' ?>">
                        </div>
                    </div>
                </div>
                
                <!-- Form Group 2 (First, Middle and Last Names) -->
                <div class='form-group'>
                    <div class="col-md-3 col-md-offset-1 <?php echo (in_array(6, $_GET) || in_array(14, $_GET)) ? 'has-error' : (isset($_GET[f_name]) ? 'has-success' : '') ?> ">
                        <div>
                        <?php
                        if (in_array(6, $_GET) || in_array(7, $_GET) || in_array(14, $_GET) || in_array(15, $_GET) || in_array(16, $_GET)) {
                            echo "<h5 class=";
                            echo (in_array(6, $_GET)) ? "'text-danger text-center bg-danger message'>First Name Required" : (in_array(14, $_GET) ? "'text-danger text-center bg-danger message'>Invalid First Name" : "'message'>&nbsp");
                            echo "</h5>";
                        }
                        if (in_array(14, $_GET)) {?>
                            <div class='input-group'>
                                <input type='text' id='f_name' name='f_name' class='form-control' placeholder='First Name' value="<?php echo isset($_GET[f_name]) ? htmlspecialchars(substr($_GET[f_name], 1, -1)) : '' ?>">
                                <a data-toggle="modal" data-target="#nameHelp" class="btn btn-default input-group-addon">&nbsp?&nbsp</a>
                            </div>
                            <?php } else { ?>
                                <input type='text' id='f_name' name='f_name' class='form-control' placeholder='First Name' value="<?php echo isset($_GET[f_name]) ? htmlspecialchars(substr($_GET[f_name], 1, -1)) : '' ?>">
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-md-3 <?php echo (in_array(15, $_GET)) ? 'has-error' : (isset($_GET[m_name]) ? 'has-success' : '') ?>">
                        <div>
                        <?php
                        if (in_array(6, $_GET) || in_array(7, $_GET) || in_array(14, $_GET) || in_array(15, $_GET) || in_array(16, $_GET)) {
                            echo "<h5 class=";
                            echo (in_array(15, $_GET)) ? "'text-danger text-center bg-danger message'>Invalid Middle Name" : "'message'>&nbsp";
                            echo "</h5>";
                        }
                        if (in_array(15, $_GET)) {?>
                            <div class='input-group'>
                                <input type='text' id='m_name' name='m_name' class='form-control' placeholder='Middle Name (Optional)' value="<?php echo isset($_GET[m_name]) ? htmlspecialchars(substr($_GET[m_name], 1, -1)) : '' ?>">
                                <a data-toggle='modal' data-target='#nameHelp' class="btn btn-default input-group-addon">&nbsp?&nbsp</a>
                            </div>
                        <?php } else { ?>
                            <input type='text' id='m_name' name='m_name' class='form-control' placeholder='Middle Name (Optional)' value="<?php echo isset($_GET[m_name]) ? htmlspecialchars(substr($_GET[m_name], 1, -1)) : '' ?>">
                        <?php } ?>
                        </div>
                    </div>
                    <div class="col-md-3 <?php echo (in_array(7, $_GET) || in_array(16, $_GET)) ? 'has-error' : (isset($_GET[l_name]) ? 'has-success' : '') ?>">
                        <div>
                        <?php
                        if (in_array(6, $_GET) || in_array(7, $_GET) || in_array(14, $_GET) || in_array(15, $_GET) || in_array(16, $_GET)) {
                            echo "<h5 class=";
                            echo (in_array(7, $_GET)) ? "'text-danger text-center bg-danger message'>Last Name Required" : (in_array(16, $_GET) ? "'text-danger text-center bg-danger message'>Invalid Last Name" : "'message'>&nbsp");
                            echo "</h5>";
                        }
                        if (in_array(16, $_GET)) {?>
                            <div class='input-group'>
                                <input type='text' id='l_name' name='l_name' class='form-control' placeholder='Last Name' value="<?php echo isset($_GET[l_name]) ? htmlspecialchars(substr($_GET[l_name], 1, -1)) : '' ?>">
                                <a data-toggle='modal' data-target='#nameHelp' class="btn btn-default input-group-addon">&nbsp?&nbsp</a>
                            </div>
                        <?php } else { ?>
                            <input type='text' id='l_name' name='l_name' class='form-control' placeholder='Last Name' value="<?php echo isset($_GET[l_name]) ? htmlspecialchars(substr($_GET[l_name], 1, -1)) : '' ?>">
                        <?php } ?>
                        </div>
                    </div>
                    <div class='col-md-1 zero_left_pad <?php echo (isset($_GET[suffix]) ? 'has-success' : '') ?>'>
                        <div>
                        <?php 
                        if (in_array(6, $_GET) || in_array(7, $_GET) || in_array(14, $_GET) || in_array(15, $_GET) || in_array(16, $_GET)) {
                            echo "<h5 class='message'>&nbsp</h5>";   
                        }?>
                            <select id='suffix' name='suffix' class='form-control'>
                                <option value=''>-</option>
                                <option value='Jr.' <?php echo ($_GET[suffix] == '\'Jr.\'') ? 'selected' : '' ?> >Jr.</option>
                                <option value='Sr.' <?php echo ($_GET[suffix] == '\'Sr.\'') ? 'selected' : '' ?> >Sr.</option>
                                <option value='I' <?php echo ($_GET[suffix] == '\'I\'') ? 'selected' : '' ?> >I</option>
                                <option value='II' <?php echo ($_GET[suffix] == '\'II\'') ? 'selected' : '' ?> >II</option>
                                <option value='III' <?php echo ($_GET[suffix] == '\'III\'') ? 'selected' : '' ?> >III</option>
                                <option value='IV' <?php echo ($_GET[suffix] == '\'IV\'') ? 'selected' : '' ?> >IV</option>
                                <option value='V' <?php echo ($_GET[suffix] == '\'V\'') ? 'selected' : '' ?> >V</option>
                                <option value='VI' <?php echo ($_GET[suffix] == '\'VI\'') ? 'selected' : '' ?> >VI</option>
                                <option value='VII' <?php echo ($_GET[suffix] == '\'VII\'') ? 'selected' : '' ?> >VII</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Form Group 3 (Password) -->
                <div class='form-group'>
                    <div class="col-md-5 col-md-offset-1 <?php echo (in_array(8, $_GET)) ? 'has-error' : (isset($_GET[new_password]) ? 'has-success' : '') ?>">
                        <div>
                        <?php
                        if (in_array(8, $_GET) || in_array(9, $_GET) || in_array(10, $_GET)) {
                            echo "<h5 class=";
                            echo (in_array(8, $_GET)) ? "'text-danger text-center bg-danger message'>Password Required" : "'message'>&nbsp";
                            echo "</h5>";
                        }?>
                            <input type='password' id='new_password' name='new_password' class='form-control' placeholder='Password' value="<?php echo isset($_GET[new_password]) ? htmlspecialchars(substr($_GET[new_password], 1, -1)) : '' ?>">
                        </div>
                    </div>
                    <div class="col-md-5 <?php echo (in_array(9, $_GET) || in_array(10, $_GET)) ? 'has-error' : (isset($_GET[confirm_password]) ? 'has-success' : '') ?>">
                        <div>
                        <?php
                        if (in_array(8, $_GET) || in_array(9, $_GET) || in_array(10, $_GET)) {
                            echo "<h5 class=";
                            echo (in_array(9, $_GET)) ? "'text-danger text-center bg-danger message'>Password Confirmation Required" : (in_array(10, $_GET) ? "'text-danger text-center bg-danger message'>Passwords Do Not Match" : "'message'>&nbsp");
                            echo "</h5>";
                        }?>
                            <input type='password' id='confirm_password' name='confirm_password' class='form-control' placeholder='Confirm Password' value="<?php echo isset($_GET[confirm_password]) ? htmlspecialchars(substr($_GET[confirm_password], 1, -1)) : '' ?>">
                        </div>
                    </div>
                </div>
                
                <!-- Form Group 4 (Submit) -->
                <div class='form-group'>
                    <div class='col-md-offset-1 col-md-10'>
                        <input type='submit' value='New User' class='form-control btn-default'>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modals -->
    
    <!-- Username Help Popup -->
    <div id='usernameHelp' class='modal fade' role='dialog'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title text-center'>Username Requirements</h4>
                </div>
                <div class='modal-body'>
                    <ul class='col-md-offset-2'>
                        <li>Must be between 6-30 characters</li>
                        <li>Must contain at least one letter (upper or lower-case)</li>
                        <li>May also contain numbers, dots, and underscores</li>
                        <li>Must NOT start or end with a dot or an underscore</li>
                        <li>Must NOT contain consecutive dots or underscores</li>
                        <li>Must be unique (no duplicate Usernames)</li>
                    </ul>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-danger col-md-12' data-dismiss='modal'><span class='glyphicon glyphicon-remove-sign' aria-hidden='true'></span></button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Name Help Popup -->
    <div id='nameHelp' class='modal fade' role='dialog'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title text-center'>Name Requirements</h4>
                </div>
                <div class='modal-body'>
                    <ul class='col-md-offset-2'>
                        <li>Must be between 1-30 characters</li>
                        <li>Must contain at least one letter (upper or lower-case)</li>
                        <li>May also contain hyphens, spaces, and apostrophes</li>
                        <li>Must NOT start or end with a space or a hyphen</li>
                        <li>Must NOT contain consecutive spaces, hyphens, or apostrophes</li>
                        <li>Middle Name and Suffix are optional</li>
                    </ul>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-danger col-md-12' data-dismiss='modal'><span class='glyphicon glyphicon-remove-sign' aria-hidden='true'></span></button>
                </div>
            </div>
        </div>
    </div>

    <?php require_once "templates/footer.php";