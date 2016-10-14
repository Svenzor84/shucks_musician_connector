<!--
 Filename:    header.php
 Author:      Steve Ross-Byers
 Description: header template for Shucks! website; includes navigation bar with login form
-->

<?php require_once ('intermediates/db_access.php'); ?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Shucks! | <?=$_SESSION[page]?></title>
        <meta charset="utf-8">
        
        <!-- Bootstrap CSS link -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
        <link rel="stylesheet" href="/style/main.css">
        
    </head>
    <body>
        <div id='holder'>
        <nav class ='navbar navbar-inverse navbar-fixed-top'>
            <div class='container-fluid'>
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="<?=$base_url?>">Shucks!</a>
                </div>
                
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="navbar-collapse-1">
                    
                    <!-- Nav links stacked on the left -->
                    <ul class='nav navbar-nav navbar-left'>
                        <?php echo isset($_SESSION[username]) ? "<li> <a href='$_base_url/my_profile.php'> $_SESSION[username] <span class='glyphicon glyphicon-user' aria-hidden='true'></span> </a> </li>" : "" ?>
                    </ul>
                    
                    <!-- Nav links stacked on the right -->
                    <ul class="nav navbar-nav navbar-right">
                        
                        <?php if (!isset($_SESSION[username])) {
                            echo '<li><a href="' . $base_url . 'login.php">Login</a></li>';
                        } else {
                            echo '<li><a href="' . $base_url . '/intermediates/log_user.php">Logout</a></li>';
                        }
                        ?>
                        
                    </ul>
                </div>
            </div>
        </nav>
        <div class='jumbotron'>
            <div class= 'container-fluid'>
                