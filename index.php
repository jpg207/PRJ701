<?php
    //This page Simply redirects traffic to the controller
    // error reporting
    ini_set( 'display_errors', 1 );
    error_reporting( E_ALL );
    // session start
    session_start();
    $_SESSION['Active'] = 'True';

    header("Location: Controllers/controller.php")
?>
