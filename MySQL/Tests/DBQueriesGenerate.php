<?php
    class DBQueriesGenerate {

        public function Query($query){
            $sql = new DBConnection("compcreator");
            $DBObject = $sql->query("$query");
            $result = $DBObject->fetch_assoc();
            return $result;
        }

        public function GetItem($Name, $Budget, $Query){
            $Query = "SELECT componentidentifyer.`CompID`, `CompName`, `CompPrice`, `CompLink`, componentdetail.* FROM componentidentifyer INNER JOIN componentdetail ON componentidentifyer.CompID = componentdetail.CompID WHERE componentidentifyer.CompPrice <= " . $Budget . " " . $Query;
            print $Query;
            $rows = array();
            $sql = new DBConnection("compcreator");
            $result = $sql->query($Query);
            while($row = mysqli_fetch_assoc($result)){
                foreach ($row as $col => $value) {
                    $rows[$col]= $value;
                }
            }
            if (isset($rows['CompName']) && $rows['CompName'] != "") {
                return $rows;
            }else {
                unset($rows);
            }
        }

        public function DBGetCase($Budget, $FormFactor){
            $rows = $this->GetItem("systemcase", $Budget, "AND (componentdetail.DetailTitle = 'TypeOfChassis' AND componentdetail.DetailValue = '$FormFactor') AND (componentdetail.DetailTitle = 'SupportedMotherboards' AND componentdetail.DetailValue != '0') AND (componentdetail.DetailTitle = 'MaximumLengthOfVideoCard' AND componentdetail.DetailValue != '0') AND (componentdetail.DetailTitle = 'MaxCPUCoolerHeight' AND componentdetail.DetailValue != '0') ORDER BY componentidentifyer.CompPrice DESC LIMIT 1");
            return $rows;
        }

        public function DBGetMOBO($Budget, $supportedMOBOFormats, $WIFI){
            $collection = array();
            unset($MOBOFormatsQuery);
            foreach($supportedMOBOFormats as $Format){
                $Format = ltrim($Format);
                if (isset($MOBOFormatsQuery)){
                    $MOBOFormatsQuery = $MOBOFormatsQuery . " OR componentdetail.FormFactor = '$Format'";
                }else{
                    $MOBOFormatsQuery = "componentdetail.FormFactor = '$Format'";
                }
            }

            $rows = $this->GetItem("motherboard", $Budget, "AND (" . $MOBOFormatsQuery . ") ORDER BY componentidentifyer.CompPrice DESC LIMIT 1");
            array_push($collection, $rows);
            if ($WIFI == "Yes" && $rows['WirelessNetwork'] != "Yes") {
                $Budget = (20/100) * $Budget;
                $wireless = $this->GetItem("wirelessadapter", $Budget, "ORDER BY TotalDataTransferRate DESC LIMIT 1");
                array_push($collection, $wireless);
            }
            return $collection;
        }

        public function DBGetCPU($Budget, $Socket){
            $rows = $this->GetItem("cpu", $Budget, "AND componentdetail.Socket = '$Socket' ORDER BY componentdetail.CPURating ASC LIMIT 1");
            return $rows;
        }

        public function DBGetGPU($Budget, $Length){
            $rows = $this->GetItem("gpu", $Budget, "AND componentdetail.Length <= $Length ORDER BY componentdetail.GPURating ASC LIMIT 1");
            return $rows;
        }

        public function DBGetHDD($Budget){
            $rows = $this->GetItem("hdd", $Budget, "ORDER BY hdd.Harddrivesize DESC LIMIT 1");
            return $rows;
        }

        public function DBGetSSD($Budget){
            $rows = $this->GetItem("ssd", $Budget, "ORDER BY ssd.Size DESC LIMIT 1");
            return $rows;
        }

        public function DBGetRAM($Budget, $Typeofmemory, $Memoryslots){
            $rows = $this->GetItem("memory", $Budget, "AND componentdetail.TypeOfMemory = '$Typeofmemory' AND componentdetail.NumberOfModules <= $Memoryslots ORDER BY componentdetail.MemoryCapacity DESC, componentdetail.PriceGB LIMIT 1");
            return $rows;
        }

        public function DBGetPSU($Budget){
            $rows = $this->GetItem("psu", $Budget, "ORDER BY componentidentifyer.CompPrice DESC LIMIT 1");
            return $rows;
        }

        public function DBGetAir($Budget, $Socket){
            $rows = $this->GetItem("aircooler", $Budget, "AND componentdetail.Socket LIKE '%" . $Socket . "%' ORDER BY componentidentifyer.CompPrice DESC LIMIT 1");
            return $rows;
        }

        public function DBGetWater($Budget, $Socket){
            $rows = $this->GetItem("watercooler", $Budget, "AND componentdetail.Socket LIKE '%" . $Socket . "%' ORDER BY componentidentifyer.CompPrice DESC LIMIT 1");
            return $rows;
        }

        public function DBODD($Budget, $Type){
            $rows = $this->GetItem("odd", $Budget, "AND " . $Type . " = 'Yes' ORDER BY componentidentifyer.CompPrice ASC LIMIT 1");
            return $rows;
        }
    }
?>
