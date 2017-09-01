<?php
    require_once('../MySQL/DBConnection.php');
    require_once('../MySQL/DBQueries.php');
    class Model
    {
        public $pages = array('Home'=> '../Content/Home.php', 'Generate'=> '../Content/Generate.php', 'Learn'=> '../Content/Learn.php', 'Contact'=> '../Content/Contact.php');
        public $questions = array('Budget'=> '../Questions/Budget.php', 'UserType'=> '../Questions/UserType.php', 'WiFi'=> '../Questions/WiFi.php', 'FormFactor'=> '../Questions/FormFactor.php', 'Programs'=> '../Questions/Programs.php', 'LocalStorage'=> '../Questions/LocalStorage.php', 'GraphicsCard'=> '../Questions/GraphicsCard.php', 'GeneratedBuild'=> '../Questions/GeneratedBuild.php');

        public function getPageContent()
        {
            return $this->pages[$_REQUEST['page']];
        }

        public function getQuestionContent()
        {
            if (isset($_REQUEST['forceQuestion'])) {
                return $this->questions[$_REQUEST['forceQuestion']];
            } else {
                if (!empty($_POST)) {
                    $_SESSION['UserAnswers'][key($_POST)]=$_POST[key($_POST)];
                }

                if (isset($_SESSION['UserAnswers']['Budget']) && !isset($_SESSION['UserAnswers']['UserType'])) {
                    return $this->questions['UserType'];
                } elseif (isset($_SESSION['UserAnswers']['UserType']) && !isset($_SESSION['UserAnswers']['WiFi'])) {
                    return $this->questions['WiFi'];
                } elseif (isset($_SESSION['UserAnswers']['WiFi']) && !isset($_SESSION['UserAnswers']['FormFactor'])) {
                    return $this->questions['FormFactor'];
                } elseif (isset($_SESSION['UserAnswers']['FormFactor']) && !isset($_SESSION['UserAnswers']['Programs'])) {
                    return $this->questions['Programs'];
                } elseif (isset($_SESSION['UserAnswers']['Programs']) && !isset($_SESSION['UserAnswers']['LocalStorage'])) {
                    return $this->questions['LocalStorage'];
                } elseif (isset($_SESSION['UserAnswers']['LocalStorage']) && !isset($_SESSION['UserAnswers']['GraphicsCard'])) {
                    return $this->questions['GraphicsCard'];
                } elseif (isset($_SESSION['UserAnswers']['GraphicsCard'])) {
                    return $this->questions['GeneratedBuild'];
                } else {
                    return $this->questions['Budget'];
                }
            }
        }

        public function processBuild(){
            $Build = array();
            $budget = $_SESSION['UserAnswers']['Budget'];
            if ($_SESSION['UserAnswers']['UserType'] == "Gamer") {
                $CPUBudget = (20 / 100) * $budget;
                $MOBOBudget = (15 / 100) * $budget;
                $GPUBudget = (30 / 100) * $budget;
                $StorageBudget = (10 / 100) * $budget;
                $RAMBudget = (10 / 100) * $budget;
                $PSUBudget = (10 / 100) * $budget;
                $CASEBudget = (5 / 100) * $budget;
            }elseif ($_SESSION['UserAnswers']['UserType'] == "Home/School/Business") {
                $CPUBudget = (30 / 100) * $budget;
                $MOBOBudget = (15 / 100) * $budget;
                $GPUBudget = (10 / 100) * $budget;
                $StorageBudget = (10 / 100) * $budget;
                $RAMBudget = (10 / 100) * $budget;
                $PSUBudget = (15 / 100) * $budget;
                $CASEBudget = (10 / 100) * $budget;
            }else {
                $CPUBudget = (30 / 100) * $budget;
                $MOBOBudget = (10 / 100) * $budget;
                $GPUBudget = (20 / 100) * $budget;
                $StorageBudget = (15 / 100) * $budget;
                $RAMBudget = (10 / 100) * $budget;
                $PSUBudget = (10 / 100) * $budget;
                $CASEBudget = (5 / 100) * $budget;
            }

            $DBQueries = new DBQueries();
            $result = $DBQueries->DBGetCPU($CPUBudget);
            $Build['CPU']=$result;
            $result = $DBQueries->DBGetMOBO();
            $result = $DBQueries->DBGetGPU();
            if($_SESSION['UserAnswers']['LocalStorage'] == "SSD"){
                $result = $DBQueries->DBGetSSD();
            }elseif ($_SESSION['UserAnswers']['LocalStorage'] == "HDD") {
                $result = $DBQueries->DBGetHDD();
            }else {
                $result = $DBQueries->DBGetSSD();
                $result = $DBQueries->DBGetHDD();
            }
            $result = $DBQueries->DBGetRAM();
            $result = $DBQueries->DBGetPSU();
            $result = $DBQueries->DBGetCase();
            return $Build;

        }
    }
