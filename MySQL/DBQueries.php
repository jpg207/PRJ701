<?php
    class DBQueries {

        public function Query($query){
            $sql = new DBConnection("compcreator");
            $DBObject = $sql->query("$query");
            $result = $DBObject->fetch_assoc();
            return $result;
        }

        public function DBGetCPU($CPUBudget){
            $rows = array();
            $sql = new DBConnection("compcreator");
            $result = $sql->query("SELECT * FROM component INNER JOIN cpu ON component.CompID = cpu.CompID WHERE component.CompPrice < $CPUBudget ORDER BY cpu.Clockfrequency DESC LIMIT 1");
            while($row = mysqli_fetch_assoc($result)){
                foreach ($row as $col => $value) {
                    $rows[$col]= $value;
                }
            }
            return $rows;
        }

        public function DBGetCase($CASEBudget, $FormFactor){
            $rows = array();
            $sql = new DBConnection("compcreator");
            $result = $sql->query("SELECT * FROM component INNER JOIN systemcase ON component.CompID = systemcase.CompID WHERE component.CompPrice < $CASEBudget AND systemcase.Typeofchassis = '$FormFactor' AND NOT systemcase.Format = '0' ORDER BY component.CompPrice DESC LIMIT 1");
            while($row = mysqli_fetch_assoc($result)){
                foreach ($row as $col => $value) {
                    $rows[$col]= $value;
                }
            }
            return $rows;
        }

        public function DBGetMOBO($MOBOBudget, $Socket, $supportedMOBOFormats){
            $rows = array();
            unset($MOBOFormatsQuery);
            foreach($supportedMOBOFormats as $Format){
                if (isset($MOBOFormatsQuery)){
                    $MOBOFormatsQuery = $MOBOFormatsQuery . " OR motherboard.Formfactor = '$Format'";
                }else{
                    $MOBOFormatsQuery = "motherboard.Formfactor = '$Format'";
                }
            }
            $sql = new DBConnection("compcreator");
            $result = $sql->query("SELECT * FROM component INNER JOIN motherboard ON component.CompID = motherboard.CompID WHERE component.CompPrice < $MOBOBudget AND " . $MOBOFormatsQuery . " AND motherboard.Socket = '$Socket' ORDER BY component.CompPrice DESC LIMIT 1");
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
            $result = $sql->query("SELECT * FROM component INNER JOIN gpu ON component.CompID = gpu.CompID WHERE component.CompPrice < $GPUBudget ORDER BY component.CompPrice DESC LIMIT 1");
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
            $result = $sql->query("SELECT * FROM component INNER JOIN hdd ON component.CompID = hdd.CompID WHERE component.CompPrice < $HDDBudget ORDER BY hdd.Harddrivesize DESC LIMIT 1");
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
            $result = $sql->query("SELECT * FROM component INNER JOIN ssd ON component.CompID = ssd.CompID WHERE component.CompPrice < $SSDBudget ORDER BY ssd.Size DESC LIMIT 1");
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
            $result = $sql->query("SELECT * FROM component INNER JOIN memory ON component.CompID = memory.CompID WHERE component.CompPrice < $RAMBudget AND memory.Typeofmemory = '$Typeofmemory' AND memory.Numberofmodules <= $Memoryslots ORDER BY memory.Memorycapacity DESC LIMIT 1");
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
            $result = $sql->query("SELECT * FROM component INNER JOIN psu ON component.CompID = psu.CompID WHERE component.CompPrice < $PSUBudget ORDER BY component.CompPrice DESC LIMIT 1");
            while($row = mysqli_fetch_assoc($result)){
                foreach ($row as $col => $value) {
                    $rows[$col]= $value;
                }
            }
            return $rows;
        }
    }
?>
