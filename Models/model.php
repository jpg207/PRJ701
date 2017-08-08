<?php
    require_once('../MySQL/DBConnection.php');
    require_once('../MySQL/DBQueries.php');
    class Model{
        public $pages = array('Home'=> '../Content/Home.php', 'Generate'=> '../Content/Generate.php', 'Learn'=> '../Content/Learn.php', 'Contact'=> '../Content/Contact.php');

        public function getPageContent() {
            return $this->pages[$_REQUEST['page']];
        }
    }
?>
