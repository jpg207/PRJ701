<?php
    require_once('../MySQL/DBConnection.php');
    require_once('../MySQL/DBQueriesGenerate.php');
    require_once('../MySQL/DBQueriesSuggestions.php');
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
            $Build = array();//Array of current build
            $Choices = $_SESSION['UserAnswers'];//Loads user choices locally
            $budget = $Choices['Budget'];//Gets user budget
            //Weighting system, Based on users usage type this system determines how much of the budget will be spent on each component
            if ($Choices['UserType'] == "Gamer") {
                $ComponentBudget = array('Case' => ((7 / 100) * $budget), 'MotherBoard' => ((10 / 100) * $budget), 'CPU' => ((20 / 100) * $budget), 'GPU' => ((30 / 100) * $budget), 'HDD' => ((0 / 100) * $budget), 'SSD' => ((0 / 100) * $budget), 'RAM' => ((10 / 100) * $budget), 'Storage' => ((13 / 100) * $budget), 'PSU' => ((7 / 100) * $budget), 'CPUCooler' => ((3/100) * $budget));
            }elseif ($Choices['UserType'] == "Home/School/Business") {
                $ComponentBudget = array('Case' => ((10 / 100) * $budget), 'MotherBoard' => ((15 / 100) * $budget), 'CPU' => ((30 / 100) * $budget), 'GPU' => ((10 / 100) * $budget), 'HDD' => ((0 / 100) * $budget), 'SSD' => ((0 / 100) * $budget), 'RAM' => ((7 / 100) * $budget), 'Storage' => ((10 / 100) * $budget), 'PSU' => ((5 / 100) * $budget), 'CPUCooler' => ((3/100) * $budget));
            }else {
                $ComponentBudget = array('Case' => ((5 / 100) * $budget), 'MotherBoard' => ((10 / 100) * $budget), 'CPU' => ((30 / 100) * $budget), 'GPU' => ((20 / 100) * $budget), 'HDD' => ((0 / 100) * $budget), 'SSD' => ((0 / 100) * $budget), 'RAM' => ((10 / 100) * $budget), 'Storage' => ((15 / 100) * $budget), 'PSU' => ((7 / 100) * $budget), 'CPUCooler' => ((3/100) * $budget));
            }

            foreach ($ComponentBudget as &$Budget) {
                $Budget = (110/100) * $Budget;
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
            $DBQueriesSuggestions = new DBQueriesSuggestions();
            try {
                $Build['Case'] = $DBQueriesGenerate->DBGetCase($ComponentBudget['Case'], $Choices['FormFactor']);//Gets a Case based on the budget and stores it  as part of the current build
                $Build['Case']['Alts'] = $DBQueriesSuggestions->DBGetCaseAlt($ComponentBudget['Case'], $Build['Case']['TypeOfChassis']);

                if (isset($Build['Case']['CompName']) && $Build['Case']['CompName'] != "") {
                    $supportedMOBOFormats = explode(',', $Build['Case']['SupportedMotherboards']);//Explodes the string of supported motherboard formats of the case by "," into an array
                    $collection = $DBQueriesGenerate->DBGetMOBO($ComponentBudget['MotherBoard'], $supportedMOBOFormats, $Choices['WiFi']);//Gets a CPU based on the budget, CPU Socket and Supported motherboard formats of the case and stores it as part of the current build
                    $Build['MotherBoard'] = $collection[0];
                    if (isset($collection[1])) {
                        $Build['WirelessAdapter'] = $collection[1];
                        $ComponentBudget['WirelessAdapter'] = ((20/100) * $ComponentBudget['MotherBoard']);
                    }
                    $Build['MotherBoard']['Alts'] = $DBQueriesSuggestions->DBGetMOBOAlt($ComponentBudget['MotherBoard'], $supportedMOBOFormats);

                }else {
                    throw new Exception ("Could not find a case that fit your budget and formfactor, midi towers tend to have the best mix of compatibility and price");
                }

                if (isset($Build['MotherBoard']['CompName']) && $Build['MotherBoard']['CompName'] != "") {
                    $Build['CPU'] = $DBQueriesGenerate->DBGetCPU($ComponentBudget['CPU'], $Build['MotherBoard']['Socket']);//Gets a CPU based on the budget and stores it as part of the current build
                    $Build['CPU']['Alts'] = $DBQueriesSuggestions->DBGetCPUAlt($ComponentBudget['CPU'], $Build['MotherBoard']['Socket'], $Build['CPU']['CPURating']);
                }else {
                    throw new Exception("Could not find a motherboard that fit your requirements");
                }

                $Build['GPU'] = $DBQueriesGenerate->DBGetGPU($ComponentBudget['GPU'], $Build['Case']['MaximumLengthOfVideoCard']);//Gets a GPU based on the budget and stores it as part of the current build
                $Build['GPU']['Alts'] = $DBQueriesSuggestions->DBGetGPUAlt($ComponentBudget['GPU'], $Build['GPU']['GPURating'], $Build['Case']['MaximumLengthOfVideoCard']);

                $Build['RAM'] = $DBQueriesGenerate->DBGetRAM($ComponentBudget['RAM'], $Build['MotherBoard']['TypeOfMemory'], $Build['MotherBoard']['MemorySlots']);//Gets RAM based on the budget and stores it as part of the current build
                $Build['RAM']['Alts'] = $DBQueriesSuggestions->DBGetRAMAlt($ComponentBudget['RAM'], $Build['MotherBoard']['TypeOfMemory'], $Build['MotherBoard']['MemorySlots'], $Build['RAM']['MemoryCapacity']);

                //Depeneding on wether the user picked SSD only, HDD only or Mixed storage, they will be got based on the budget and added to the current build
                if($Choices['LocalStorage'] == "SSD"){
                    $Build['SSD'] = $DBQueriesGenerate->DBGetSSD($ComponentBudget['Storage']);
                    $Build['SSD']['Alts'] = $DBQueriesSuggestions->DBGetSSDAlt($ComponentBudget['Storage'], $Build['SSD']['Size']);
                }elseif ($Choices['LocalStorage'] == "HDD") {
                    $Build['HDD'] = $DBQueriesGenerate->DBGetHDD($ComponentBudget['Storage']);
                    $Build['HDD']['Alts'] = $DBQueriesSuggestions->DBGetHDDAlt($ComponentBudget['Storage'], $Build['HDD']['HardDriveSize']);
                }else {
                    //If Mixed is picked, the budget is split based on the higher cost of SSD's
                    $ComponentBudget['SSD'] = (65/100) * $ComponentBudget['Storage'];
                    $Build['SSD'] = $DBQueriesGenerate->DBGetSSD($ComponentBudget['SSD']);
                    $Build['SSD']['Alts'] = $DBQueriesSuggestions->DBGetSSDAlt($ComponentBudget['SSD'], $Build['SSD']['Size']);

                    $ComponentBudget['HDD'] = (35/100) * $ComponentBudget['Storage'];
                    $Build['HDD'] = $DBQueriesGenerate->DBGetHDD($ComponentBudget['HDD']);
                    $Build['HDD']['Alts'] = $DBQueriesSuggestions->DBGetHDDAlt($ComponentBudget['Storage'], $Build['HDD']['HardDriveSize']);
                }

                $Build['PSU'] = $DBQueriesGenerate->DBGetPSU($ComponentBudget['PSU']);//Gets a PSU based on the budget and stores it as part of the current build
                $Build['PSU']['Alts'] = $DBQueriesSuggestions->DBGetPSUAlt($ComponentBudget['PSU']);

                preg_match("/([\d]{2,}|[\w]{2,}[\d].)/", $Build['MotherBoard']['Socket'], $Socket);
                if ($Choices['Cooler'] == "Water" && $Build['Case']['WaterCooling'] == "Yes") {
                    $Build['CPUCooler'] = $DBQueriesGenerate->DBGetWater($ComponentBudget['CPUCooler'], $Socket[0]);
                }elseif ($Choices['Cooler'] == "Air" || $Build['CPU']['BoxVersion'] == "without fan/cooler") {
                    if ($Choices['Cooler'] == "No") {
                        $ComponentBudget['CPUCooler'] = $ComponentBudget['CPUCooler'] / 2;
                    }
                    $Build['CPUCooler'] = $DBQueriesGenerate->DBGetAir($ComponentBudget['CPUCooler'], $Socket[0], $Build['Case']['MaxCPUCoolerHeight']);
                }

                if ($Choices['OpticalDrive'] == "Bluray") {
                    $Build['OpticalDrive'] = $DBQueriesGenerate->DBODD(9999, "Bluray");
                }elseif ($Choices['OpticalDrive'] == "DVD") {
                    $Build['OpticalDrive'] = $DBQueriesGenerate->DBODD(9999, "DVD");
                }

                $Build['ComponentBudget'] = $ComponentBudget;
            } catch (Exception $e) {
                $Build = $e;
            }

            return $Build;//Returns the current build back to the front end for display
        }
    }
