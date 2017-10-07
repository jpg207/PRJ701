<?php
    class DBQueriesGenerate
    {
        public function Query($query)
        {
            $sql = new DBConnection("compcreator");
            $DBObject = $sql->query("$query");
            $result = $DBObject->fetch_assoc();
            return $result;
        }

        public function Budget($BudgetIn)
        {
            $Budgetplus = $BudgetIn + ($BudgetIn/3);
            $Budgetminus = $BudgetIn - ($BudgetIn/3);
            $Budgets = array($BudgetIn, $Budgetplus, $Budgetminus);
            return $Budgets;
        }

        public function GetItem($Name, $Budgetin, $QueryConditions, $OrderBy)
        {
            $Budgets = $this->Budget($Budgetin);
            $count = 0;
            $Component = array();
            $Alt = array();

            foreach ($Budgets as $Budget) {
                $Components = array();
                $Item = array();
                $Query = "SELECT DISTINCT componentidentifyer.* FROM componentidentifyer INNER JOIN componentdetail ON componentidentifyer.CompID = componentdetail.CompID WHERE componentidentifyer.CompPrice <= " . $Budget . " AND componentidentifyer.CompCategory = '" . $Name . "' " . $OrderBy ."";
                $sql = new DBConnection("compcreator");
                $DetailResult = $sql->query($Query);
                while ($Temp = mysqli_fetch_assoc($DetailResult)) {
                    array_push($Item, $Temp);
                }
                foreach ($Item as $key => $value) {
                    foreach ($QueryConditions as $Condition) {
                        if ($Condition != "") {
                            $Condition = "AND " . $Condition;
                        }
                        $Query = "SELECT `CompID` FROM componentdetail WHERE `CompID` = '" . $value['CompID'] . "' " . $Condition;
                        $sql = new DBConnection("compcreator");
                        $ChildResult = $sql->query($Query);
                        $ChildResult = mysqli_fetch_assoc($ChildResult);
                        if (!isset($ChildResult)) {
                            unset($Item[$key]);
                            break;
                        }
                    }
                    if (isset($Item[$key]) && $count == 0) {
                        $ID = $value['CompID'];
                        $Component = $value;
                        break;
                    } elseif(isset($Item[$key])) {
                        $ID = $value['CompID'];
                        $Alt[$ID] = $value;
                        break;
                    }
                }
                $ComponentDetail = array();
                $Query = "SELECT `DetailTitle`, `DetailValue`, `DetailValueNumeric` FROM componentdetail  WHERE `CompID` = " . $ID;
                $sql = new DBConnection("compcreator");
                $Result = $sql->query($Query);
                while ($Rows = mysqli_fetch_assoc($Result)) {
                    $ComponentDetail[$Rows['DetailTitle']] = $Rows;
                }
                if (isset($ComponentDetail)) {
                    if ($count == 0) {
                        $Component['ComponentDetail'] = $ComponentDetail;
                    } else {
                        $Alt[$ID]['AltDetails'] = $ComponentDetail;
                    }
                }
                $count = $count + 1;
            }
            $Component['ComponentDetailAlts'] = $Alt;
            if (isset($Component['CompName']) && $Component['CompName'] != "") {
                return $Component;
            } else {
                unset($Component);
            }
        }

        public function DBGetCase($Budget, $FormFactor)
        {
            $QueryConditions = array("componentdetail.DetailTitle = 'Type Of Chassis' AND componentdetail.DetailValue = '$FormFactor'", "componentdetail.DetailTitle = 'Supported Motherboards' AND componentdetail.DetailValue != '0'", "componentdetail.DetailTitle = 'Maximum Length Of Video Card' AND componentdetail.DetailValue != '0'", "componentdetail.DetailTitle = 'Max CPU Cooler Height' AND componentdetail.DetailValue != '0'");
            $rows = $this->GetItem("systemcase", $Budget, $QueryConditions, "ORDER BY componentidentifyer.CompPrice DESC");
            return $rows;
        }

        public function DBGetMOBO($Budget, $supportedMOBOFormats, $WIFI)
        {
            #print_r($supportedMOBOFormats);
            $collection = array();
            unset($MOBOFormatsQuery);
            foreach ($supportedMOBOFormats as $Format) {
                $Format = ltrim($Format);
                if (isset($MOBOFormatsQuery)) {
                    $MOBOFormatsQuery = $MOBOFormatsQuery . " OR componentdetail.DetailValue = '$Format'";
                } else {
                    $MOBOFormatsQuery = "componentdetail.DetailTitle = 'Form Factor' AND (componentdetail.DetailValue = '$Format'";
                }
            }
            $MOBOFormatsQuery = $MOBOFormatsQuery . ")";
            $QueryConditions = array($MOBOFormatsQuery);
            $rows = $this->GetItem("motherboard", $Budget, $QueryConditions, "ORDER BY componentidentifyer.CompPrice DESC");
            array_push($collection, $rows);
            if ($WIFI == "Yes" && $rows['ComponentDetail']['Wireless network']['DetailValue'] != "Yes") {
                $Budget = (20/100) * $Budget;
                $QueryConditions = array("");
                $wireless = $this->GetItem("WirelessAdapters", $Budget, $QueryConditions, "ORDER BY (componentdetail.DetailTitle = 'Total Data Transfer Rate'), componentdetail.DetailValueNumeric DESC");
                array_push($collection, $wireless);
            }
            return $collection;
        }

        public function DBGetCPU($Budget, $Socket)
        {
            $QueryConditions = array("componentdetail.DetailTitle = 'Socket' AND componentdetail.DetailValue = '$Socket'");
            $rows = $this->GetItem("cpu", $Budget, $QueryConditions, "ORDER BY componentidentifyer.CompRating ASC ");
            return $rows;
        }

        public function DBGetGPU($Budget, $Length)
        {
            $QueryConditions = array("componentdetail.DetailTitle = 'Length' AND componentdetail.DetailValueNumeric <= '$Length' ");
            $rows = $this->GetItem("gpu", $Budget, $QueryConditions, "ORDER BY componentidentifyer.CompRating ASC ");
            return $rows;
        }

        public function DBGetHDD($Budget)
        {
            $QueryConditions = array("");
            $rows = $this->GetItem("hdd", $Budget, $QueryConditions, "ORDER BY (componentdetail.DetailTitle = 'Hard drive size'), componentdetail.DetailValueNumeric DESC ");
            return $rows;
        }

        public function DBGetSSD($Budget)
        {
            $QueryConditions = array("");
            $rows = $this->GetItem("ssd", $Budget, $QueryConditions, "ORDER BY (componentdetail.DetailTitle = 'Size'), componentdetail.DetailValueNumeric DESC ");
            return $rows;
        }

        public function DBGetRAM($Budget, $Typeofmemory, $Memoryslots)
        {
            $QueryConditions = array("componentdetail.DetailTitle = 'Type of memory' AND componentdetail.DetailValue = '$Typeofmemory'", "componentdetail.DetailTitle = 'Number of modules' AND componentdetail.DetailValueNumeric <= $Memoryslots");
            $rows = $this->GetItem("memory", $Budget, $QueryConditions, "ORDER BY (componentdetail.DetailTitle = 'MemoryCapacity'), componentdetail.DetailValueNumeric DESC");
            return $rows;
        }

        public function DBGetPSU($Budget)
        {
            $QueryConditions = array("");
            $rows = $this->GetItem("psu", $Budget, $QueryConditions, "ORDER BY componentidentifyer.CompPrice DESC ");
            return $rows;
        }

        public function DBGetAir($Budget, $Socket)
        {
            $QueryConditions = array("componentdetail.DetailTitle = 'Socket' AND componentdetail.DetailValue LIKE '%$Socket%'");
            $rows = $this->GetItem("aircooler", $Budget, $QueryConditions, "ORDER BY componentidentifyer.CompPrice DESC ");
            return $rows;
        }

        public function DBGetWater($Budget, $Socket)
        {
            $QueryConditions = array("componentdetail.DetailTitle = 'Socket' AND componentdetail.DetailValue LIKE '%$Socket%'");
            $rows = $this->GetItem("watercooler", $Budget, $QueryConditions, "ORDER BY componentidentifyer.CompPrice DESC ");
            return $rows;
        }

        public function DBODD($Budget, $Type)
        {
            $QueryConditions = array("componentdetail.DetailTitle = '$Type' AND componentdetail.DetailValue = 'Yes'");
            $rows = $this->GetItem("odd", $Budget, $QueryConditions, "ORDER BY componentidentifyer.CompPrice ASC ");
            return $rows;
        }
    }
