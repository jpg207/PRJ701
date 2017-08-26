<?php
    require_once('../MySQL/DBConnection.php');
    require_once('../MySQL/DBQueries.php');
    class Model{
        public $pages = array('Home'=> '../Content/Home.php', 'Generate'=> '../Content/Generate.php', 'Learn'=> '../Content/Learn.php', 'Contact'=> '../Content/Contact.php');
        public $questions = array('Budget'=> '../Questions/Budget.php', 'UserType'=> '../Questions/UserType.php', 'WiFi'=> '../Questions/WiFi.php', 'FormFactor'=> '../Questions/FormFactor.php', 'Programs'=> '../Questions/Programs.php', 'LocalStorage'=> '../Questions/LocalStorage.php', 'GraphicsCard'=> '../Questions/GraphicsCard.php');

        public function getPageContent() {
            return $this->pages[$_REQUEST['page']];
        }

        public function getQuestionContent() {
            if(isset($_REQUEST['forceQuestion'])){
                return $this->questions[$_REQUEST['forceQuestion']];
            }else{
                if ( !empty($_POST)){
                    $_SESSION['UserAnswers'][key($_POST)]=$_POST[key($_POST)];
                }

                if(isset($_SESSION['UserAnswers']['Budget']) && !isset($_SESSION['UserAnswers']['UserType'])){
                    return $this->questions['UserType'];
                }elseif (isset($_SESSION['UserAnswers']['UserType']) && !isset($_SESSION['UserAnswers']['WiFi'])) {
                    return $this->questions['WiFi'];
                }elseif (isset($_SESSION['UserAnswers']['WiFi']) && !isset($_SESSION['UserAnswers']['FormFactor'])) {
                    return $this->questions['FormFactor'];
                }elseif (isset($_SESSION['UserAnswers']['FormFactor']) && !isset($_SESSION['UserAnswers']['Programs'])) {
                    return $this->questions['Programs'];
                }elseif (isset($_SESSION['UserAnswers']['Programs']) && !isset($_SESSION['UserAnswers']['LocalStorage'])) {
                    return $this->questions['LocalStorage'];
                }elseif (isset($_SESSION['UserAnswers']['LocalStorage']) && !isset($_SESSION['UserAnswers']['GraphicsCard'])) {
                    return $this->questions['GraphicsCard'];
                }else{
                    return $this->questions['Budget'];
                }
            }
        }
    }
?>
