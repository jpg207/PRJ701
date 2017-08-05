<?php
    error_reporting(E_ALL);
    session_start();

    include('../Models/Model.php');
    include('../Templates/Template.php');

    class Controller {
        function accept()
        {
            $theModel = new Model();
            $PageDisplay = new PageView();
            $PageDisplay->Render($theModel);
        }
    }

    $theController = new Controller();
    $theController->accept();
?>
