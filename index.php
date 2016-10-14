<?php
/**
* Filename:    index.php
* Author:      Steve Ross-Byers
* Description: Homepage for Shucks! website; allows users to search and browse registered musicians by instrument, name, or location
*/

session_start();
$_SESSION[page] = "Home";
require_once "templates/header.php";

if (!isset($_SESSION[username])) {
    
    //if the user was recently logged out, display the successful log out message
    if(in_array(1, $_GET)) {
        
        echo "<h5 class='text-danger text-center bg-danger message'>";
        echo "User Successfully Logged Out";
        echo "</h5>";
        
    }
    
    echo "<h1 class='text-center'>Musicians. Connect.</h1>";
    echo "<p class='text-center col-md-10 col-md-offset-1'>Join our growing community of ambitious instrumentalist, vocalist, and ensemble musicians.</p>";
    echo "<p class='text-center col-md-10 col-md-offset-1'>Search for someone nearby, set up a jam session, or hire last minute help for a gig.</p>";
    echo "<p class='lead text-center col-md-10 col-md-offset-1'>Our simple rating system and extensive style catalogue allow you to find the perfect musician for any occasion.</p>"; 
    echo "<p class='lead text-center col-md-10 col-md-offset-1'>Get ready to find your new bandmates...</p>";

} else {
    
    echo "<h2 class='text-center'>Thank you for returning, " . htmlspecialchars($_SESSION[f_name]) . ".</h2>";
}

require_once "templates/footer.php";