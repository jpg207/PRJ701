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

        public function GetItem($Name, $Budgetin, $QueryConditions, $OrderBy, $AltLock)
        {
            $Budgets = $this->Budget($Budgetin);
            $count = 0;
            $Component = array();
            $Alt = array();
            $AltSpecLock = "";

            foreach ($Budgets as $Budget) {
                if ($count != 0 && $AltLock != "") {
                    $AltSpecLock = "AND (componentdetail.DetailTitle = '" . $AltLock . "' AND componentdetail.DetailValue = '" . $Component['ComponentDetail'][$AltLock]['DetailValue'] . "')";
                }
                $Components = array();
                $Item = array();
                $Query = "SELECT DISTINCT componentidentifyer.* FROM componentidentifyer INNER JOIN componentdetail ON componentidentifyer.CompID = componentdetail.CompID WHERE componentidentifyer.CompPrice <= " . $Budget . " AND componentidentifyer.CompCategory = '" . $Name . "' " . $AltSpecLock . " " . $OrderBy ."";
                $sql = new DBConnection("compcreator");
                #print $Query . "<br /><br />";
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
                        #print $Query . "<br /><br />";
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
                if (isset($ID)) {
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
            }
            $sql->closeConnection();
            $Component['ComponentDetailAlts'] = $Alt;
            if (isset($Component['CompName']) && $Component['CompName'] != "") {
                return $Component;
            } else {
                unset($Component);
            }
        }

        public function DBGetCase($Budget, $FormFactor)
        {
            $AltLock = "";
            $QueryConditions = array("componentdetail.DetailTitle = 'Type Of Chassis' AND componentdetail.DetailValue = '$FormFactor'", "componentdetail.DetailTitle = 'Supported Motherboards' AND componentdetail.DetailValue != '0'", "componentdetail.DetailTitle = 'Maximum Length Of Video Card' AND componentdetail.DetailValue != '0'", "componentdetail.DetailTitle = 'Max CPU Cooler Height' AND componentdetail.DetailValue != '0'");
            $rows = $this->GetItem("systemcase", $Budget, $QueryConditions, "ORDER BY componentidentifyer.CompPrice DESC" ,$AltLock);
            return $rows;
        }

        public function DBGetCPU($Budget, $InvalidSockets)
        {
            $AltLock = "Socket";
            $InvalidSocket = "";
            foreach ($InvalidSockets as $value) {
                if ($InvalidSocket = "") {
                    $InvalidSocket = "AND";
                }
                $InvalidSocket = $InvalidSocket . " AND (componentdetail.DetailTitle = 'Socket' AND componentdetail.DetailValue !=  " . $value . ")";
            }
            $QueryConditions = array();
            $rows = $this->GetItem("cpu", $Budget, $QueryConditions, "$InvalidSocket ORDER BY componentidentifyer.CompRating ASC", $AltLock);
            return $rows;
        }

        public function DBGetMOBO($Budget, $supportedMOBOFormats, $Socket, $WIFI)
        {
            $AltLock = "";
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
            $QueryConditions = array("componentdetail.DetailTitle = 'Socket' AND componentdetail.DetailValue = '$Socket'", "$MOBOFormatsQuery");
            $rows = $this->GetItem("motherboard", $Budget, $QueryConditions, "ORDER BY componentidentifyer.CompPrice DESC", $AltLock);
            array_push($collection, $rows);


            if ($WIFI == "Yes" && $rows['ComponentDetail']['Wireless network']['DetailValue'] != "Yes") {
                $AltLock = "";
                $Budget = (20/100) * $Budget;
                $QueryConditions = array();
                $wireless = $this->GetItem("WirelessAdapters", $Budget, $QueryConditions, "AND componentdetail.DetailTitle = 'Total Data Transfer Rate' ORDER BY componentdetail.DetailValueNumeric DESC", $AltLock);
                $collection[1] = $wireless;
            }
            return $collection;
        }

        public function DBGetGPU($Budget, $Length)
        {
            $AltLock = "";
            $QueryConditions = array("componentdetail.DetailTitle = 'Length' AND componentdetail.DetailValueNumeric <= '$Length' ");
            $rows = $this->GetItem("gpu", $Budget, $QueryConditions, "ORDER BY componentidentifyer.CompRating ASC", $AltLock);
            return $rows;
        }

        public function DBGetHDD($Budget)
        {
            $AltLock = "";
            $QueryConditions = array();
            $rows = $this->GetItem("hdd", $Budget, $QueryConditions, "AND componentdetail.DetailTitle = 'Hard drive size' ORDER BY componentdetail.DetailValueNumeric DESC", $AltLock);
            return $rows;
        }

        public function DBGetSSD($Budget)
        {
            $AltLock = "";
            $QueryConditions = array();
            $rows = $this->GetItem("ssd", $Budget, $QueryConditions, "AND componentdetail.DetailTitle = 'Size' ORDER BY componentdetail.DetailValueNumeric DESC", $AltLock);
            return $rows;
        }

        public function DBGetRAM($Budget, $Typeofmemory, $Memoryslots)
        {
            $AltLock = "";
            $QueryConditions = array("componentdetail.DetailTitle = 'Type of memory' AND componentdetail.DetailValue = '$Typeofmemory'", "componentdetail.DetailTitle = 'Number of modules' AND componentdetail.DetailValueNumeric <= $Memoryslots");
            $rows = $this->GetItem("memory", $Budget, $QueryConditions, "AND componentdetail.DetailTitle = 'Memory Capacity' ORDER BY componentdetail.DetailValueNumeric DESC, componentidentifyer.CompPrice ASC", $AltLock);
            return $rows;
        }

        public function DBGetPSU($Budget)
        {
            $AltLock = "";
            $QueryConditions = array("");
            $rows = $this->GetItem("psu", $Budget, $QueryConditions, "ORDER BY componentidentifyer.CompPrice DESC", $AltLock);
            return $rows;
        }

        public function DBGetAir($Budget, $Socket)
        {
            $AltLock = "";
            $QueryConditions = array("componentdetail.DetailTitle = 'Socket' AND componentdetail.DetailValue LIKE '%$Socket%'");
            $rows = $this->GetItem("aircooler", $Budget, $QueryConditions, "ORDER BY componentidentifyer.CompPrice DESC ", $AltLock);
            return $rows;
        }

        public function DBGetWater($Budget, $Socket)
        {
            $AltLock = "";
            $QueryConditions = array("componentdetail.DetailTitle = 'Socket' AND componentdetail.DetailValue LIKE '%$Socket%'");
            $rows = $this->GetItem("watercooler", $Budget, $QueryConditions, "ORDER BY componentidentifyer.CompPrice DESC ", $AltLock);
            return $rows;
        }

        public function DBODD($Budget, $Type)
        {
            $AltLock = "";
            $QueryConditions = array("componentdetail.DetailTitle = '$Type' AND componentdetail.DetailValue = 'Yes'");
            $rows = $this->GetItem("odd", $Budget, $QueryConditions, "ORDER BY componentidentifyer.CompPrice ASC ", $AltLock);
            return $rows;
        }
    }
