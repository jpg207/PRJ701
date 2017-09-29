<?php
    class DBQueriesGenerate {

        public function Query($query){
            $sql = new DBConnection("compcreator");
            $DBObject = $sql->query("$query");
            $result = $DBObject->fetch_assoc();
            return $result;
        }

        public function GetItem($Name, $Budget, $Query){
            $Query = "SELECT component.`CompID`, `CompName`, `CompPrice`, `CompLink`, " . $Name . ".* FROM component INNER JOIN " . $Name . " ON component.CompID = " . $Name . ".CompID WHERE component.CompPrice <= " . $Budget . " " . $Query;
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
            $rows = $this->GetItem("systemcase", $Budget, "AND systemcase.TypeOfChassis = '$FormFactor' AND NOT systemcase.SupportedMotherboards = '0' AND systemcase.MaximumLengthOfVideoCard != '0' AND systemcase.MaxCPUCoolerHeight != '0' ORDER BY component.CompPrice DESC LIMIT 1");
            return $rows;
        }

        public function DBGetMOBO($Budget, $supportedMOBOFormats, $WIFI){
            $collection = array();
            unset($MOBOFormatsQuery);
            foreach($supportedMOBOFormats as $Format){
                $Format = ltrim($Format);
                if (isset($MOBOFormatsQuery)){
                    $MOBOFormatsQuery = $MOBOFormatsQuery . " OR motherboard.FormFactor = '$Format'";
                }else{
                    $MOBOFormatsQuery = "motherboard.FormFactor = '$Format'";
                }
            }

            $rows = $this->GetItem("motherboard", $Budget, "AND (" . $MOBOFormatsQuery . ") ORDER BY component.CompPrice DESC LIMIT 1");
            array_push($collection, $rows);
            if ($WIFI == "Yes" && $rows['WirelessNetwork'] != "Yes") {
                $Budget = (20/100) * $Budget;
                $wireless = $this->GetItem("wirelessadapter", $Budget, "ORDER BY TotalDataTransferRate DESC LIMIT 1");
                array_push($collection, $wireless);
            }
            return $collection;
        }

        public function DBGetCPU($Budget, $Socket){
            $rows = $this->GetItem("cpu", $Budget, "AND cpu.Socket = '$Socket' ORDER BY cpu.CPURating ASC LIMIT 1");
            return $rows;
        }

        public function DBGetGPU($Budget, $Length){
            $rows = $this->GetItem("gpu", $Budget, "AND gpu.Length <= $Length ORDER BY gpu.GPURating ASC LIMIT 1");
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
            $rows = $this->GetItem("memory", $Budget, "AND memory.TypeOfMemory = '$Typeofmemory' AND memory.NumberOfModules <= $Memoryslots ORDER BY memory.MemoryCapacity DESC, memory.PriceGB LIMIT 1");
            return $rows;
        }

        public function DBGetPSU($Budget){
            $rows = $this->GetItem("psu", $Budget, "ORDER BY component.CompPrice DESC LIMIT 1");
            return $rows;
        }

        public function DBGetAir($Budget, $Socket){
            $rows = $this->GetItem("aircooler", $Budget, "AND aircooler.Socket LIKE '%" . $Socket . "%' ORDER BY component.CompPrice DESC LIMIT 1");
            return $rows;
        }

        public function DBGetWater($Budget, $Socket){
            $rows = $this->GetItem("watercooler", $Budget, "AND watercooler.Socket LIKE '%" . $Socket . "%' ORDER BY component.CompPrice DESC LIMIT 1");
            return $rows;
        }

        public function DBODD($Budget, $Type){
            $rows = $this->GetItem("odd", $Budget, "AND " . $Type . " = 'Yes' ORDER BY component.CompPrice ASC LIMIT 1");
            return $rows;
        }
    }
?>
