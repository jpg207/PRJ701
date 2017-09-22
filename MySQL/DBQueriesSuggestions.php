<?php
    class DBQueriesSuggestions {

        public function Query($query){
            $sql = new DBConnection("compcreator");
            $DBObject = $sql->query("$query");
            $result = $DBObject->fetch_assoc();
            return $result;
        }

        public function DBGetCaseAlt($CASEBudget, $FormFactor){
            $alts = array();
            $sql = new DBConnection("compcreator");
            $Budgetplus = $CASEBudget + ($CASEBudget/3);
            $Budgetminus = $CASEBudget - ($CASEBudget/3);
            $bugets = array($Budgetplus, $Budgetminus);
            foreach ($bugets as $buget) {
                $rows = array();
                $result = $sql->query("SELECT `CompName`, `CompPrice`, `CompLink`, `TypeOfChassis`, `Material`, `Dimensions`, `35DriveBays`, `Format`, `Volume`, `SupportedMotherboards`, `Colour`, `RoomForExpansion`, `Weight`, `25DriveBays`, `MaxCPUCoolerHeight` FROM component INNER JOIN systemcase ON component.CompID = systemcase.CompID WHERE component.CompPrice <= $buget AND systemcase.TypeOfChassis = '$FormFactor' AND NOT systemcase.SupportedMotherboards = '0' ORDER BY component.CompPrice DESC LIMIT 1");
                while($row = mysqli_fetch_assoc($result)){
                    foreach ($row as $col => $value) {
                        $rows[$col]= $value;
                    }
                }
                if (isset($rows['CompName']) && $rows['CompName'] != "") {
                    array_push($alts, $rows);
                }
            }
            return $alts;
        }


        public function DBGetMOBOAlt($MOBOBudget, $supportedMOBOFormats, $WIFI){
            $alts = array();
            $Budgetplus = $MOBOBudget + ($MOBOBudget/3);
            $Budgetminus = $MOBOBudget - ($MOBOBudget/3);
            $bugets = array($Budgetplus, $Budgetminus);

            unset($MOBOFormatsQuery);
            foreach($supportedMOBOFormats as $Format){
                $Format = ltrim($Format);
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

            foreach ($bugets as $buget) {
                $rows = array();
                $result = $sql->query("SELECT `CompName`, `CompPrice`, `CompLink`, `FormFactor`, `EthernetConnection`, `Bluetooth`, `PCIExpressx16`, `MemorySlots`, `Chipset`, `MaximumAmountOfMemory`, `WirelessNetwork`, `M2`, `SATA6Gbs` FROM component INNER JOIN motherboard ON component.CompID = motherboard.CompID WHERE component.CompPrice <= $buget AND (" . $MOBOFormatsQuery . ")" . $WIFIQuery . " ORDER BY component.CompPrice DESC LIMIT 1");
                while($row = mysqli_fetch_assoc($result)){
                    foreach ($row as $col => $value) {
                        $rows[$col]= $value;
                    }
                }
                if (isset($rows['CompName']) && $rows['CompName'] != "") {
                    array_push($alts, $rows);
                }
            }
            return $alts;
        }

        public function DBGetCPUAlt($CPUBudget, $Socket, $Rating){
            $alts = array();

            $Budgetplus = $CPUBudget + ($CPUBudget/3);
            $Budgetminus = $CPUBudget - ($CPUBudget/3);
            $bugets = array($Budgetplus, $Budgetminus);

            $Plus = " < ";
            $Minus = " > ";
            $symbols = array($Plus, $Minus);

            $DESC = " DESC";
            $ASC = " ASC";
            $Order = array($DESC, $ASC);

            $Loop = 0;
            $sql = new DBConnection("compcreator");
            foreach ($symbols as $symbol) {
                $rows = array();
                $result = $sql->query("SELECT `CompName`, `CompPrice`, `CompLink`, `ClockFrequency`, `ProductPage`, `L3Cache`, `TurboBoostCore`, `CPUType`, `BoxVersion`, `NumberOfThreads`, `64bitProcessor`, `L2Cache`, `Socket`, `NumberOfCores`, `GraphicsProcessor`, `ReleaseYear`, `ThermalDesignPower`, `IntegratedGraphics`, `Virtualization`, `CPURating` FROM component INNER JOIN cpu ON component.CompID = cpu.CompID WHERE  cpu.Socket = '$Socket' AND cpu.CPURating $symbol $Rating AND component.CompPrice <= $bugets[$Loop] ORDER BY cpu.CPURating $Order[$Loop] LIMIT 1");
                while($row = mysqli_fetch_assoc($result)){
                    foreach ($row as $col => $value) {
                        $rows[$col]= $value;
                    }
                }
                if (isset($rows['CompName']) && $rows['CompName'] != "") {
                    array_push($alts, $rows);
                }
                $Loop += 1;
            }
            return $alts;
        }

        public function DBGetGPUAlt($GPUBudget, $Rating){
            $alts = array();

            $Budgetplus = $GPUBudget + ($GPUBudget/3);
            $Budgetminus = $GPUBudget - ($GPUBudget/3);
            $bugets = array($Budgetplus, $Budgetminus);

            $Plus = " < ";
            $Minus = " > ";
            $symbols = array($Plus, $Minus);

            $DESC = " DESC";
            $ASC = " ASC";
            $Order = array($DESC, $ASC);

            $Loop = 0;
            $sql = new DBConnection("compcreator");
            foreach ($symbols as $symbol) {
                $result = $sql->query("SELECT `CompName`, `CompPrice`, `CompLink`, `Cooling`, `NumberOfFans`, `GraphicsProcessor`, `MaximumResolution`, `Length`, `NumberOfSlots`, `MemoryBandwidth`, `MemoryCapacity`, `MemoryInterface`, `MemorySpeed`, `MemoryType`, `GPUBoost`, `ProcessorSpeed` , `GPURating` FROM component INNER JOIN gpu ON component.CompID = gpu.CompID WHERE gpu.GPURating $symbol $Rating AND component.CompPrice <= $bugets[$Loop] ORDER BY gpu.GPURating $Order[$Loop] LIMIT 1");
                while($row = mysqli_fetch_assoc($result)){
                    foreach ($row as $col => $value) {
                        $rows[$col]= $value;
                    }
                }
                if (isset($rows['CompName']) && $rows['CompName'] != "") {
                    array_push($alts, $rows);
                }
                $Loop += 1;
            }
            return $alts;
        }

        public function DBGetRAMAlt($RAMBudget, $Typeofmemory, $Memoryslots, $Capacity){
            $alts = array();

            $Budgetplus = $RAMBudget + ($RAMBudget/3);
            $Budgetminus = $RAMBudget - ($RAMBudget/3);
            $bugets = array($Budgetplus, $Budgetminus);

            $Plus = " < ";
            $Minus = " > ";
            $symbols = array($Plus, $Minus);

            $Loop = 0;
            $sql = new DBConnection("compcreator");
            foreach ($symbols as $symbol) {
                $rows = array();
                $result = $sql->query("SELECT `CompName`, `CompPrice`, `CompLink`, `NumberOfModules`, `MemorySpeed`, `MemoryCapacity`, `ECC`, `ReleaseYear`, `PricePerGigabyte`, `ManufacturerWarranty`, `MemoryCapacityPerModule`, `CASLatency`, `TypeOfMemory`, `ProductPage`, `Voltage` FROM component INNER JOIN memory ON component.CompID = memory.CompID WHERE component.CompPrice <= $bugets[$Loop] AND memory.TypeOfMemory = '$Typeofmemory' AND memory.NumberOfModules <= $Memoryslots AND memory.MemoryCapacity $symbol $Capacity ORDER BY memory.MemoryCapacity DESC, memory.PricePerGigabyte LIMIT 1");
                while($row = mysqli_fetch_assoc($result)){
                    foreach ($row as $col => $value) {
                        $rows[$col]= $value;
                    }
                }
                if (isset($rows['CompName']) && $rows['CompName'] != "") {
                    array_push($alts, $rows);
                }
                $Loop += 1;
            }
            return $alts;
        }

        public function DBGetHDDAlt($HDDBudget, $Capacity){
            $alts = array();

            $Budgetplus = $HDDBudget + ($HDDBudget/3);
            $Budgetminus = $HDDBudget - ($HDDBudget/3);
            $bugets = array($Budgetplus, $Budgetminus);

            $Plus = " < ";
            $Minus = " > ";
            $symbols = array($Plus, $Minus);

            $Loop = 0;
            $sql = new DBConnection("compcreator");
            foreach ($symbols as $symbol) {
                $rows = array();
                $sql = new DBConnection("compcreator");
                $result = $sql->query("SELECT `CompName`, `CompPrice`, `CompLink`, `HybridDisk`, `PriceperTeraByte`, `FormFactor`, `Interface`, `CacheSize`, `Connection`, `RotationalSpeed`, `HardDriveSize` FROM component INNER JOIN hdd ON component.CompID = hdd.CompID WHERE component.CompPrice <= $bugets[$Loop] AND hdd.HardDriveSize $symbol $Capacity ORDER BY hdd.Harddrivesize DESC LIMIT 1");
                while($row = mysqli_fetch_assoc($result)){
                    foreach ($row as $col => $value) {
                        $rows[$col]= $value;
                    }
                }
                if (isset($rows['CompName']) && $rows['CompName'] != "") {
                    array_push($alts, $rows);
                }
                $Loop += 1;
            }
            return $alts;
        }

        public function DBGetSSDAlt($SSDBudget, $Capacity){
            $alts = array();

            $Budgetplus = $SSDBudget + ($SSDBudget/3);
            $Budgetminus = $SSDBudget - ($SSDBudget/3);
            $bugets = array($Budgetplus, $Budgetminus);

            $Plus = " < ";
            $Minus = " > ";
            $symbols = array($Plus, $Minus);

            $Loop = 0;
            $sql = new DBConnection("compcreator");
            foreach ($symbols as $symbol) {
                $result = $sql->query("SELECT `CompName`, `CompPrice`, `CompLink`,  `MaximumReadSpeed`, `PricePerGigabyte`, `Interface`, `FormFactor`, `MaximumWriteSpeed`, `Size` FROM component INNER JOIN ssd ON component.CompID = ssd.CompID WHERE component.CompPrice <= $bugets[$Loop] AND ssd.Size $symbol $Capacity ORDER BY ssd.Size DESC LIMIT 1");
                while($row = mysqli_fetch_assoc($result)){
                    foreach ($row as $col => $value) {
                        $rows[$col]= $value;
                    }
                }
                if (isset($rows['CompName']) && $rows['CompName'] != "") {
                    array_push($alts, $rows);
                }
                $Loop += 1;
            }
            return $alts;
        }

        public function DBGetPSUAlt($PSUBudget){
            $alts = array();

            $Budgetplus = $PSUBudget + ($PSUBudget/3);
            $Budgetminus = $PSUBudget - ($PSUBudget/3);
            $bugets = array($Budgetplus, $Budgetminus);

            $sql = new DBConnection("compcreator");
            foreach ($bugets as $buget) {
                $result = $sql->query("SELECT `CompName`, `CompPrice`, `CompLink`, `Capacity`, `Modular`, `TemperatureControlledFan`, `NumberOfFans`, `Format`, `80PlusCertification` FROM component INNER JOIN psu ON component.CompID = psu.CompID WHERE component.CompPrice <= $buget ORDER BY component.CompPrice DESC LIMIT 1");
                while($row = mysqli_fetch_assoc($result)){
                    foreach ($row as $col => $value) {
                        $rows[$col]= $value;
                    }
                }
                if (isset($rows['CompName']) && $rows['CompName'] != "") {
                    array_push($alts, $rows);
                }
            }
            return $alts;
        }

    }
?>
