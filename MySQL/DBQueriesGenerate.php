<?php
    class DBQueriesGenerate {

        public function Query($query){
            $sql = new DBConnection("compcreator");
            $DBObject = $sql->query("$query");
            $result = $DBObject->fetch_assoc();
            return $result;
        }

        public function Budget($BudgetIn){
            $Budgetplus = $BudgetIn + ($BudgetIn/3);
            $Budgetminus = $BudgetIn - ($BudgetIn/3);
            $Budgets = array($Budgetplus, $Budgetminus);
            return $Budgets;
        }

        public function GetItem($Name, $Budget, $QueryConditions, $OrderBy){
            $Component = array();
            $Alt = array();

            $Query = "SELECT DISTINCT componentidentifyer.`CompID` FROM componentidentifyer INNER JOIN componentdetail ON componentidentifyer.CompID = componentdetail.CompID WHERE componentidentifyer.CompCategory = '" . $Name . "' " . $OrderBy ."";
            $sql = new DBConnection("compcreator");
            $DetailResult = $sql->query($Query);

            $Query = "SELECT * FROM componentidentifyer WHERE componentidentifyer.CompPrice <= " . $Budget . " AND componentidentifyer.CompCategory = '" . $Name . "'";
            #print $Query . "<br />";
            #print_r($QueryConditions);
            $Components = array();
            $sql = new DBConnection("compcreator");
            $ParentResult = $sql->query($Query);
            while($Item = mysqli_fetch_assoc($ParentResult)){
                foreach ($Item as $key => $value) {
                    if ($key == "CompID") {
                        foreach ($QueryConditions as $Condition) {
                            if ($Condition != "") {
                                $Condition = "AND " . $Condition;
                            }
                            $Query = "SELECT `CompID` FROM componentdetail WHERE `CompID` = '" . $value . "' " . $Condition;
                            $sql = new DBConnection("compcreator");
                            $ChildResult = $sql->query($Query);
                            $ChildResult = mysqli_fetch_assoc($ChildResult);
                            if(!isset($ChildResult)){
                                unset($value);
                                break;
                            }
                        }
                        #print_r($Component);
                        #echo "<br />" . $Component['CompID'] . "<br />";
                        if (isset($value)) {
                            $Components[$Item['CompID']] = $Item;
                        }
                    }
                }
            }

            #print_r($Components);
            while($DetailRows = mysqli_fetch_assoc($DetailResult)){
                foreach ($DetailRows as $DetailValue) {
                    #if ($count != 0) {
                    #    print_r($DetailValue);
                    #    echo "<br /> !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! <br />";
                    #
                    #echo $Components[$DetailValue] . "<br />";
                    #print_r($DetailResult);
                    #echo $Name . " <br />";
                    if (isset($Components[$DetailValue]) || array_key_exists($DetailValue, $Components)) {
                        $ComponentDetail = array();
                        $Component = $Components[$DetailValue];
                        $Query = "SELECT `DetailTitle`, `DetailValue`, `DetailValueNumeric` FROM componentdetail  WHERE `CompID` = " . $DetailValue . "";
                        $sql = new DBConnection("compcreator");
                        $Result = $sql->query($Query);
                        while($Rows = mysqli_fetch_assoc($Result)){
                            $ComponentDetail[$Rows['DetailTitle']] = $Rows;
                        }
                        break;
                    }
                }
                if (isset($ComponentDetail)) {
                    $Component['ComponentDetail'] = $ComponentDetail;
                    break;
                }
            }
            $Component['ComponentDetailAlts'] = $Alt;
            #print_r($Component['ComponentDetailAlts']);
            #echo "<br />";
            if (isset($Component['CompName']) && $Component['CompName'] != "") {
                return $Component;
            }else {
                unset($Component);
            }
        }

        public function DBGetCase($Budget, $FormFactor){
            $QueryConditions = array("componentdetail.DetailTitle = 'Type Of Chassis' AND componentdetail.DetailValue = '$FormFactor'", "componentdetail.DetailTitle = 'Supported Motherboards' AND componentdetail.DetailValue != '0'", "componentdetail.DetailTitle = 'Maximum Length Of Video Card' AND componentdetail.DetailValue != '0'", "componentdetail.DetailTitle = 'Max CPU Cooler Height' AND componentdetail.DetailValue != '0'");
            $rows = $this->GetItem("systemcase", $Budget, $QueryConditions,  "ORDER BY componentidentifyer.CompPrice DESC");
            return $rows;
        }

        public function DBGetMOBO($Budget, $supportedMOBOFormats, $WIFI){
            #print_r($supportedMOBOFormats);
            $collection = array();
            unset($MOBOFormatsQuery);
            foreach($supportedMOBOFormats as $Format){
                $Format = ltrim($Format);
                if (isset($MOBOFormatsQuery)){
                    $MOBOFormatsQuery = $MOBOFormatsQuery . " OR componentdetail.DetailValue = '$Format'";
                }else{
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

        public function DBGetCPU($Budget, $Socket){
            $QueryConditions = array("componentdetail.DetailTitle = 'Socket' AND componentdetail.DetailValue = '$Socket'");
            $rows = $this->GetItem("cpu", $Budget, $QueryConditions, "ORDER BY componentidentifyer.CompRating ASC ");
            return $rows;
        }

        public function DBGetGPU($Budget, $Length){
            $QueryConditions = array("componentdetail.DetailTitle = 'Length' AND componentdetail.DetailValueNumeric <= '$Length' ");
            $rows = $this->GetItem("gpu", $Budget, $QueryConditions, "ORDER BY componentidentifyer.CompRating ASC ");
            return $rows;
        }

        public function DBGetHDD($Budget){
            $QueryConditions = array("");
            $rows = $this->GetItem("hdd", $Budget, $QueryConditions, "ORDER BY (componentdetail.DetailTitle = 'Hard drive size'), componentdetail.DetailValueNumeric DESC ");
            return $rows;
        }

        public function DBGetSSD($Budget){
            $QueryConditions = array("");
            $rows = $this->GetItem("ssd", $Budget, $QueryConditions, "ORDER BY (componentdetail.DetailTitle = 'Size'), componentdetail.DetailValueNumeric DESC ");
            return $rows;
        }

        public function DBGetRAM($Budget, $Typeofmemory, $Memoryslots){
            $QueryConditions = array("componentdetail.DetailTitle = 'Type of memory' AND componentdetail.DetailValue = '$Typeofmemory'", "componentdetail.DetailTitle = 'Number of modules' AND componentdetail.DetailValueNumeric <= $Memoryslots");
            $rows = $this->GetItem("memory", $Budget, $QueryConditions, "ORDER BY (componentdetail.DetailTitle = 'MemoryCapacity'), componentdetail.DetailValueNumeric DESC");
            return $rows;
        }

        public function DBGetPSU($Budget){
            $QueryConditions = array("");
            $rows = $this->GetItem("psu", $Budget, $QueryConditions, "ORDER BY componentidentifyer.CompPrice DESC ");
            return $rows;
        }

        public function DBGetAir($Budget, $Socket){
            $QueryConditions = array("componentdetail.DetailTitle = 'Socket' AND componentdetail.DetailValue LIKE '%$Socket%'");
            $rows = $this->GetItem("aircooler", $Budget, $QueryConditions, "ORDER BY componentidentifyer.CompPrice DESC ");
            return $rows;
        }

        public function DBGetWater($Budget, $Socket){
            $QueryConditions = array("componentdetail.DetailTitle = 'Socket' AND componentdetail.DetailValue LIKE '%$Socket%'");
            $rows = $this->GetItem("watercooler", $Budget, $QueryConditions, "ORDER BY componentidentifyer.CompPrice DESC ");
            return $rows;
        }

        public function DBODD($Budget, $Type){
            $QueryConditions = array("componentdetail.DetailTitle = '$Type' AND componentdetail.DetailValue = 'Yes'");
            $rows = $this->GetItem("odd", $Budget, $QueryConditions, "ORDER BY componentidentifyer.CompPrice ASC ");
            return $rows;
        }
    }
?>
