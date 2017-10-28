<?php
    //This page Simply redirects traffic to the controller
    // error reporting
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    // session start
    session_start();
    $_SESSION['Active'] = 'True';
    $UserAnswers = array();
    $_SESSION['UserAnswers'] = $UserAnswers;
    header("Location: Controllers/Controller.php")
?>
