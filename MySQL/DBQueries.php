<?php
    class DBQueries {

        public function Query($query){
            $sql = new DBConnection("compcreator");
            $DBObject = $sql->query("$query");
            $result = $DBObject->fetch_assoc();
            return $result;
        }

        public function DBGetCase($CASEBudget, $FormFactor){
            $rows = array();
            $sql = new DBConnection("compcreator");
            $result = $sql->query("SELECT * FROM component INNER JOIN systemcase ON component.CompID = systemcase.CompID WHERE component.CompPrice <= $CASEBudget AND systemcase.TypeOfChassis = '$FormFactor' AND NOT systemcase.Format = '0' ORDER BY component.CompPrice DESC LIMIT 1");
            while($row = mysqli_fetch_assoc($result)){
                foreach ($row as $col => $value) {
                    $rows[$col]= $value;
                }
            }
            return $rows;
        }

        public function DBGetMOBO($MOBOBudget, $supportedMOBOFormats, $WIFI){
            $rows = array();
            unset($MOBOFormatsQuery);
            foreach($supportedMOBOFormats as $Format){
                if (isset($MOBOFormatsQuery)){
                    $MOBOFormatsQuery = $MOBOFormatsQuery . " OR motherboard.Formfactor = '$Format'";
                }else{
                    $MOBOFormatsQuery = "motherboard.Formfactor = '$Format'";
                }
            }

            unset($WIFIQuery);
            if($WIFI == "Yes"){
                $WIFIQuery = " AND motherboard.WirelessNetwork = 'Yes'";
            }else{
                $WIFIQuery = "";
            }
            $sql = new DBConnection("compcreator");

            $result = $sql->query("SELECT * FROM component INNER JOIN motherboard ON component.CompID = motherboard.CompID WHERE component.CompPrice <= $MOBOBudget AND (" . $MOBOFormatsQuery . ")" . $WIFIQuery . " ORDER BY component.CompPrice DESC LIMIT 1");
            while($row = mysqli_fetch_assoc($result)){
                foreach ($row as $col => $value) {
                    $rows[$col]= $value;
                }
            }
            return $rows;
        }

        public function DBGetCPU($CPUBudget, $Socket){
            $rows = array();
            $sql = new DBConnection("compcreator");
            $result = $sql->query("SELECT * FROM component INNER JOIN cpu ON component.CompID = cpu.CompID WHERE component.CompPrice <= $CPUBudget AND cpu.Socket = '$Socket' ORDER BY cpu.CPURating ASC LIMIT 1");
            while($row = mysqli_fetch_assoc($result)){
                foreach ($row as $col => $value) {
                    $rows[$col]= $value;
                }
            }
            return $rows;
        }

        public function DBGetGPU($GPUBudget){
            $rows = array();
            $sql = new DBConnection("compcreator");
            $result = $sql->query("SELECT * FROM component INNER JOIN gpu ON component.CompID = gpu.CompID WHERE component.CompPrice <= $GPUBudget ORDER BY gpu.GPURating ASC LIMIT 1");
            while($row = mysqli_fetch_assoc($result)){
                foreach ($row as $col => $value) {
                    $rows[$col]= $value;
                }
            }
            return $rows;
        }

        public function DBGetHDD($HDDBudget){
            $rows = array();
            $sql = new DBConnection("compcreator");
            $result = $sql->query("SELECT * FROM component INNER JOIN hdd ON component.CompID = hdd.CompID WHERE component.CompPrice <= $HDDBudget ORDER BY hdd.Harddrivesize DESC LIMIT 1");
            while($row = mysqli_fetch_assoc($result)){
                foreach ($row as $col => $value) {
                    $rows[$col]= $value;
                }
            }
            return $rows;
        }

        public function DBGetSSD($SSDBudget){
            $rows = array();
            $sql = new DBConnection("compcreator");
            $result = $sql->query("SELECT * FROM component INNER JOIN ssd ON component.CompID = ssd.CompID WHERE component.CompPrice <= $SSDBudget ORDER BY ssd.Size DESC LIMIT 1");
            while($row = mysqli_fetch_assoc($result)){
                foreach ($row as $col => $value) {
                    $rows[$col]= $value;
                }
            }
            return $rows;
        }

        public function DBGetRAM($RAMBudget, $Typeofmemory, $Memoryslots){
            $rows = array();
            $sql = new DBConnection("compcreator");
            $result = $sql->query("SELECT * FROM component INNER JOIN memory ON component.CompID = memory.CompID WHERE component.CompPrice < $RAMBudget AND memory.TypeOfMemory = '$Typeofmemory' AND memory.NumberOfModules <= $Memoryslots ORDER BY memory.MemoryCapacity DESC, memory.PricePerGigabyte LIMIT 1");
            while($row = mysqli_fetch_assoc($result)){
                foreach ($row as $col => $value) {
                    $rows[$col]= $value;
                }
            }
            return $rows;
        }

        public function DBGetPSU($PSUBudget){
            $rows = array();
            $sql = new DBConnection("compcreator");
            $result = $sql->query("SELECT * FROM component INNER JOIN psu ON component.CompID = psu.CompID WHERE component.CompPrice <= $PSUBudget ORDER BY component.CompPrice DESC LIMIT 1");
            while($row = mysqli_fetch_assoc($result)){
                foreach ($row as $col => $value) {
                    $rows[$col]= $value;
                }
            }
            return $rows;
        }
    }
?>
