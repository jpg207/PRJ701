<?php
    require_once('../MySQL/DBConnection.php');
    require_once('../MySQL/DBQueries.php');
    class Model{
        public $pages = array('Home'=> '../Content/home.php');

        public function getPageContent() {
            return $this->pages[$_REQUEST['page']];
        }
    }
?>
