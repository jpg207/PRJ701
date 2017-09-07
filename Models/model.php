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
            $Build = array();//Array of current build
            $Choices = $_SESSION['UserAnswers'];//Loads user choices locally
            $budget = $Choices['Budget'];//Gets user budget
            //Weighting system, Based on users usage type this system determines how much of the budget will be spent on each component
            if ($Choices['UserType'] == "Gamer") {
                $CPUBudget = (20 / 100) * $budget;
                $MOBOBudget = (15 / 100) * $budget;
                $GPUBudget = (30 / 100) * $budget;
                $StorageBudget = (10 / 100) * $budget;
                $RAMBudget = (10 / 100) * $budget;
                $PSUBudget = (10 / 100) * $budget;
                $CASEBudget = (5 / 100) * $budget;
            }elseif ($Choices['UserType'] == "Home/School/Business") {
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
            $Build['CPU'] = $DBQueries->DBGetCPU($CPUBudget);//Gets a CPU based on the budget and stores it as part of the current build

            $Build['Case'] = $DBQueries->DBGetCase($CASEBudget, $Choices['FormFactor']);//Gets a Case based on the budget and stores it  as part of the current build

            $supportedMOBOFormats = explode(',', $Build['Case']['Format']);//Explodes the string of supported motherboard formats of the case by "," into an array
            $Build['MOBO'] = $DBQueries->DBGetMOBO($MOBOBudget, $Build['CPU']['Socket'], $supportedMOBOFormats);//Gets a CPU based on the budget, CPU Socket and Supported motherboard formats of the case and stores it as part of the current build

            $Build['GPU'] = $DBQueries->DBGetGPU($GPUBudget);//Gets a GPU based on the budget and stores it as part of the current build

            //Depeneding on wether the user picked SSD only, HDD only or Mixed storage, they will be got based on the budget and added to the current build
            if($_SESSION['UserAnswers']['LocalStorage'] == "SSD"){
                $Build['SSD'] = $DBQueries->DBGetSSD($StorageBudget);
            }elseif ($_SESSION['UserAnswers']['LocalStorage'] == "HDD") {
                $Build['HDD'] = $DBQueries->DBGetHDD($StorageBudget);
            }else {
                //If Mixed is picked, the budget is split based on the higher cost of SSD's
                $HDDBudget = (35/100) * $StorageBudget;
                $Build['HDD'] = $DBQueries->DBGetHDD($HDDBudget);

                $SSDBudget = (65/100) * $StorageBudget;
                $Build['SSD'] = $DBQueries->DBGetSSD($SSDBudget);
            }

            $Build['RAM'] = $DBQueries->DBGetRAM($RAMBudget, $Build['MOBO']['Typeofmemory'], $Build['MOBO']['Memoryslots']);//Gets RAM based on the budget and stores it as part of the current build

            $Build['PSU'] = $DBQueries->DBGetPSU($PSUBudget);//Gets a PSU based on the budget and stores it as part of the current build

            return $Build;//Returns the current build back to the front end for display
        }
    }
