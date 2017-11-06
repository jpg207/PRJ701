<?php
    require_once('../MySQL/DBConnection.php');
    require_once('../MySQL/DBQueriesGenerate.php');
    class Model
    {
        public $pages = array('Home'=> '../Content/Home.php', 'Generate'=> '../Content/Generate.php', 'Learn'=> '../Content/Learn.php', 'Contact'=> '../Content/Contact.php');
        public $questions = array('Budget'=> '../Questions/Budget.php', 'UserType'=> '../Questions/UserType.php', 'WiFi'=> '../Questions/WiFi.php', 'FormFactor'=> '../Questions/FormFactor.php', 'Programs'=> '../Questions/Programs.php', 'LocalStorage'=> '../Questions/LocalStorage.php', 'GraphicsCard'=> '../Questions/GraphicsCard.php', 'OpticalDrive'=> '../Questions/OpticalDrive.php', 'Cooler'=> '../Questions/Cooler.php', 'GeneratedBuild'=> '../Questions/GeneratedBuild.php');

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
                } elseif (isset($_SESSION['UserAnswers']['FormFactor']) && !isset($_SESSION['UserAnswers']['LocalStorage'])) {
                    return $this->questions['LocalStorage'];
                } elseif (isset($_SESSION['UserAnswers']['LocalStorage']) && !isset($_SESSION['UserAnswers']['GraphicsCard'])) {
                    return $this->questions['GraphicsCard'];
                } elseif (isset($_SESSION['UserAnswers']['GraphicsCard'])&& !isset($_SESSION['UserAnswers']['Cooler'])) {
                    return $this->questions['Cooler'];
                } elseif (isset($_SESSION['UserAnswers']['Cooler'])&& !isset($_SESSION['UserAnswers']['OpticalDrive'])) {
                    return $this->questions['OpticalDrive'];
                } elseif (isset($_SESSION['UserAnswers']['OpticalDrive'])) {
                    return $this->questions['GeneratedBuild'];
                } else {
                    return $this->questions['Budget'];
                }
            }
        }

        public function processBuild(){
            $warnings = array();
            unset($_SESSION['Warnings']);
            $Build = array();//Array of current build
            $Choices = $_SESSION['UserAnswers'];//Loads user choices locally
            $budget = $Choices['Budget'];//Gets user budget
            //Weighting system, Based on users usage type this system determines how much of the budget will be spent on each component
            if ($Choices['UserType'] == "Gamer") {
                $ComponentBudget = array('Case' => ((7 / 100) * $budget), 'MotherBoard' => ((10 / 100) * $budget), 'CPU' => ((20 / 100) * $budget), 'GPU' => ((33 / 100) * $budget), 'HDD' => ((0 / 100) * $budget), 'SSD' => ((0 / 100) * $budget), 'RAM' => ((7 / 100) * $budget), 'Storage' => ((13 / 100) * $budget), 'PSU' => ((8 / 100) * $budget), 'CPUCooler' => ((3/100) * $budget));
            }elseif ($Choices['UserType'] == "Home/School/Business") {
                $ComponentBudget = array('Case' => ((10 / 100) * $budget), 'MotherBoard' => ((12 / 100) * $budget), 'CPU' => ((23 / 100) * $budget), 'GPU' => ((10 / 100) * $budget), 'HDD' => ((0 / 100) * $budget), 'SSD' => ((0 / 100) * $budget), 'RAM' => ((7 / 100) * $budget), 'Storage' => ((20 / 100) * $budget), 'PSU' => ((5 / 100) * $budget), 'CPUCooler' => ((3/100) * $budget));
            }else {
                $ComponentBudget = array('Case' => ((5 / 100) * $budget), 'MotherBoard' => ((10 / 100) * $budget), 'CPU' => ((30 / 100) * $budget), 'GPU' => ((20 / 100) * $budget), 'HDD' => ((0 / 100) * $budget), 'SSD' => ((0 / 100) * $budget), 'RAM' => ((10 / 100) * $budget), 'Storage' => ((15 / 100) * $budget), 'PSU' => ((7 / 100) * $budget), 'CPUCooler' => ((3/100) * $budget));
            }

            foreach ($ComponentBudget as &$Budget) {
                $Budget = (105/100) * $Budget;
            }

            if ($Choices['GraphicsCard'] == "No") {
                $ComponentBudget['GPU'] = $ComponentBudget['GPU'] / 2;
                $Items = count($ComponentBudget);
                $UnusedFunds = $ComponentBudget['GPU'] / $Items;
                foreach ($ComponentBudget as &$Budget) {
                    $Budget = $Budget + $UnusedFunds;
                }
            }

            $DBQueriesGenerate = new DBQueriesGenerate();
            try {
                $Build['Case'] = $DBQueriesGenerate->DBGetCase($ComponentBudget['Case'], $Choices['FormFactor']);//Gets a Case based on the budget and stores it  as part of the current build

                if (isset($Build['Case']['CompName']) && $Build['Case']['CompName'] != "") {
                    while (!isset($Build['CPU']) && !isset($Build['MotherBoard'])) {
                        unset($Build['CPU']);
                        unset($Build['MotherBoard']);
                        $Build['CPU'] = $DBQueriesGenerate->DBGetCPU($ComponentBudget['CPU']);

                        $supportedMOBOFormats = explode(',', $Build['Case']['ComponentDetail']['Supported motherboards']['DetailValue']);
                        $collection = $DBQueriesGenerate->DBGetMOBO($ComponentBudget['MotherBoard'], $supportedMOBOFormats, $Build['CPU']['ComponentDetail']['Socket']['DetailValue'], $Choices['WiFi']);
                        $Build['MotherBoard'] = $collection[0];
                        if (isset($collection[1])) {
                            $Build['WirelessAdapter'] = $collection[1];
                            $ComponentBudget['WirelessAdapter'] = ((20/100) * $ComponentBudget['MotherBoard']);
                        }
                    }
                }else {
                    throw new Exception ("Could not find a case that fit your budget and formfactor, midi towers tend to have the best mix of compatibility and price");
                }

                $Build['GPU'] = $DBQueriesGenerate->DBGetGPU($ComponentBudget['GPU'], $Build['Case']['ComponentDetail']['Maximum length of video card']['DetailValueNumeric']);//Gets a GPU based on the budget and stores it as part of the current build

                $Build['RAM'] = $DBQueriesGenerate->DBGetRAM($ComponentBudget['RAM'], $Build['MotherBoard']['ComponentDetail']['Type of memory']['DetailValue'], $Build['MotherBoard']['ComponentDetail']['Memory slots']['DetailValueNumeric']);//Gets RAM based on the budget and stores it as part of the current build

                //Depeneding on wether the user picked SSD only, HDD only or Mixed storage, they will be got based on the budget and added to the current build
                if($Choices['LocalStorage'] == "SSD"){
                    $Build['SSD'] = $DBQueriesGenerate->DBGetSSD($ComponentBudget['Storage']);
                }elseif ($Choices['LocalStorage'] == "HDD") {
                    $Build['HDD'] = $DBQueriesGenerate->DBGetHDD($ComponentBudget['Storage']);
                }else {
                    //If Mixed is picked, the budget is split based on the higher cost of SSD's
                    $ComponentBudget['SSD'] = (65/100) * $ComponentBudget['Storage'];
                    $Build['SSD'] = $DBQueriesGenerate->DBGetSSD($ComponentBudget['SSD']);

                    $ComponentBudget['HDD'] = (35/100) * $ComponentBudget['Storage'];
                    $Build['HDD'] = $DBQueriesGenerate->DBGetHDD($ComponentBudget['HDD']);
                }

                $Build['PSU'] = $DBQueriesGenerate->DBGetPSU($ComponentBudget['PSU']);//Gets a PSU based on the budget and stores it as part of the current build

                preg_match("/([\d]{2,}|[\w]{2,}[\d])/", $Build['MotherBoard']['ComponentDetail']['Socket']['DetailValue'], $Socket);

                if ($Choices['Cooler'] == "Water" && isset($Build['Case']['ComponentDetail']['Water cooling']['DetailValue']) && $Build['Case']['ComponentDetail']['Water cooling']['DetailValue'] == "Yes") {
                    $Build['CPUCooler'] = $DBQueriesGenerate->DBGetWater($ComponentBudget['CPUCooler'], $Socket[0]);
                    if ($Build['CPUCooler'] == null) {
                        $Build['CPUCooler'] = $DBQueriesGenerate->DBGetAir($ComponentBudget['CPUCooler'], $Socket[0], $Build['Case']['ComponentDetail']['Max CPU cooler height']['DetailValue']);
                        $warnings['WaterCooler'] = "WaterCooler could not be found, defaulting to air";
                    }
                }elseif ($Choices['Cooler'] == "Air" || $Build['CPU']['ComponentDetail']['Box version']['DetailValue'] == "without fan/cooler") {
                    if ($Choices['Cooler'] == "No") {
                        $ComponentBudget['CPUCooler'] = $ComponentBudget['CPUCooler'] / 2;
                    }
                    $Build['CPUCooler'] = $DBQueriesGenerate->DBGetAir($ComponentBudget['CPUCooler'], $Socket[0], $Build['Case']['ComponentDetail']['Max CPU cooler height']['DetailValue']);
                }

                if ($Choices['OpticalDrive'] == "Bluray") {
                    $Build['OpticalDrive'] = $DBQueriesGenerate->DBODD(9999, "Bluray");
                }elseif ($Choices['OpticalDrive'] == "DVD") {
                    $Build['OpticalDrive'] = $DBQueriesGenerate->DBODD(9999, "DVD");
                }

                $Build['ComponentBudget'] = $ComponentBudget;
            } catch (Exception $e) {
                $Build = $e->getMessage();
            }
            $_SESSION['Warnings']  = $warnings;
            return $Build;//Returns the current build back to the front end for display
        }
    }
