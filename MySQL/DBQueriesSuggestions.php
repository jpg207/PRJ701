<?php
    class DBQueriesSuggestions {

        public function Budget($BudgetIn){
            $Budgetplus = $BudgetIn + ($BudgetIn/3);
            $Budgetminus = $BudgetIn - ($BudgetIn/3);
            $Budgets = array($Budgetplus, $Budgetminus);
            return $Budgets;
        }

        public function Symbol(){
            $Plus = " < ";
            $Minus = " > ";
            $Symbols = array($Plus, $Minus);
            return $Symbols;
        }

        public function Order(){
            $DESC = " DESC";
            $ASC = " ASC";
            $Order = array($DESC, $ASC);
            return $Order;
        }

        public function GetAlts($Query, &$alts){
            $rows = array();
            $sql = new DBConnection("compcreator");
            $result = $sql->query($Query);
            while($row = mysqli_fetch_assoc($result)){
                foreach ($row as $col => $value) {
                    $rows[$col]= $value;
                }
            }
            if (isset($rows['CompName']) && $rows['CompName'] != "") {
                array_push($alts, $rows);
            }
        }

        public function Query($query){
            $sql = new DBConnection("compcreator");
            $DBObject = $sql->query("$query");
            $result = $DBObject->fetch_assoc();
            return $result;
        }

        public function DBGetCaseAlt($CASEBudget, $FormFactor){
            $alts = array();
            $Budgets = $this->Budget($CASEBudget);
            foreach ($Budgets as $Budget) {
                $this->GetAlts("SELECT component.`CompID`, `CompName`, `CompPrice`, `CompLink`, `TypeOfChassis`, `Material`, `Dimensions`, `35DriveBays`, `Format`, `Volume`, `SupportedMotherboards`, `Colour`, `RoomForExpansion`, `Weight`, `25DriveBays`, `MaxCPUCoolerHeight` FROM component INNER JOIN systemcase ON component.CompID = systemcase.CompID WHERE component.CompPrice <= $Budget AND systemcase.TypeOfChassis = '$FormFactor' AND NOT systemcase.SupportedMotherboards = '0' ORDER BY component.CompPrice DESC LIMIT 1", $alts);
            }
            return $alts;
        }


        public function DBGetMOBOAlt($MOBOBudget, $supportedMOBOFormats){
            $alts = array();
            $Budgets = $this->Budget($MOBOBudget);
            unset($MOBOFormatsQuery);
            foreach($supportedMOBOFormats as $Format){
                $Format = ltrim($Format);
                if (isset($MOBOFormatsQuery)){
                    $MOBOFormatsQuery = $MOBOFormatsQuery . " OR motherboard.Formfactor = '$Format'";
                }else{
                    $MOBOFormatsQuery = "motherboard.Formfactor = '$Format'";
                }
            }

            foreach ($Budgets as $Budget) {
                $this->GetAlts("SELECT component.`CompID`, `CompName`, `CompPrice`, `CompLink`, `FormFactor`, `EthernetConnection`, `Bluetooth`, `PCIExpressx16`, `MemorySlots`, `Chipset`, `MaximumAmountOfMemory`, `WirelessNetwork`, `M2`, `SATA6Gbs` FROM component INNER JOIN motherboard ON component.CompID = motherboard.CompID WHERE component.CompPrice <= $Budget AND (" . $MOBOFormatsQuery . ") ORDER BY component.CompPrice DESC LIMIT 1", $alts);
            }
            return $alts;
        }

        public function DBGetCPUAlt($CPUBudget, $Socket, $Rating){
            $alts = array();

            $Budgets = $this->Budget($CPUBudget);
            $symbols = $this->Symbol();
            $Order = $this->Order();

            $Loop = 0;

            foreach ($Budgets as $Budget) {
                $this->GetAlts("SELECT component.`CompID`, `CompName`, `CompPrice`, `CompLink`, `ClockFrequency`, `CPUType`, `BoxVersion`, `NumberOfThreads`, `Socket`, `NumberOfCores`, `ThermalDesignPower`, `IntegratedGraphics`, `CPURating` FROM component INNER JOIN cpu ON component.CompID = cpu.CompID WHERE  cpu.Socket = '$Socket' AND cpu.CPURating $symbols[$Loop] $Rating AND component.CompPrice <= $Budget ORDER BY cpu.CPURating $Order[$Loop] LIMIT 1", $alts);
                $Loop += 1;
            }
            return $alts;
        }

        public function DBGetGPUAlt($GPUBudget, $Rating, $Length){
            $alts = array();
            $Budgets = $this->Budget($GPUBudget);
            $symbols = $this->Symbol();
            $Order = $this->Order();
            $Loop = 0;
            foreach ($Budgets as $Budget) {
                $this->GetAlts("SELECT component.`CompID`, `CompName`, `CompPrice`, `CompLink`, `Cooling`, `NumberOfFans`, `GraphicsProcessor`, `MaximumResolution`, `Length`, `NumberOfSlots`, `MemoryBandwidth`, `MemoryCapacity`, `MemoryInterface`, `MemorySpeed`, `MemoryType`, `GPUBoost`, `ProcessorSpeed` , `GPURating` FROM component INNER JOIN gpu ON component.CompID = gpu.CompID WHERE gpu.GPURating $symbols[$Loop] $Rating AND component.CompPrice <= $Budget AND gpu.Length <= $Length ORDER BY gpu.GPURating $Order[$Loop] LIMIT 1", $alts);
                $Loop += 1;
            }
            return $alts;
        }

        public function DBGetRAMAlt($RAMBudget, $Typeofmemory, $Memoryslots, $Capacity){
            $alts = array();

            $Budgets = $this->Budget($RAMBudget);
            $symbols = $this->Symbol();

            $Loop = 0;

            foreach ($Budgets as $Budget) {
                $this->GetAlts("SELECT component.`CompID`, `CompName`, `CompPrice`, `CompLink`, `NumberOfModules`, `MemorySpeed`, `MemoryCapacity`, `ECC`, `ReleaseYear`, `PriceGB`, `ManufacturerWarranty`, `MemoryCapacityPerModule`, `CASLatency`, `TypeOfMemory`, `ProductPage`, `Voltage` FROM component INNER JOIN memory ON component.CompID = memory.CompID WHERE component.CompPrice <= $Budget AND memory.TypeOfMemory = '$Typeofmemory' AND memory.NumberOfModules <= $Memoryslots AND memory.MemoryCapacity $symbols[$Loop] $Capacity ORDER BY memory.MemoryCapacity DESC, memory.PriceGB LIMIT 1", $alts);
                $Loop += 1;
            }
            return $alts;
        }

        public function DBGetHDDAlt($HDDBudget, $Capacity){
            $alts = array();
            $Budgets = $this->Budget($HDDBudget);
            $symbols = $this->Symbol();
            $Loop = 0;
            foreach ($Budgets as $Budget) {
                $this->GetAlts("SELECT component.`CompID`, `CompName`, `CompPrice`, `CompLink`, `HybridDisk`, `PriceTB`, `FormFactor`, `Interface`, `CacheSize`, `Connection`, `RotationalSpeed`, `HardDriveSize` FROM component INNER JOIN hdd ON component.CompID = hdd.CompID WHERE component.CompPrice <= $Budget AND hdd.HardDriveSize $symbols[$Loop] $Capacity ORDER BY hdd.Harddrivesize DESC LIMIT 1", $alts);
                $Loop += 1;
            }
            return $alts;
        }

        public function DBGetSSDAlt($SSDBudget, $Capacity){
            $alts = array();
            $Budgets = $this->Budget($SSDBudget);
            $symbols = $this->Symbol();
            $Loop = 0;
            foreach ($Budgets as $Budget) {
                $this->GetAlts("SELECT component.`CompID`, `CompName`, `CompPrice`, `CompLink`,  `MaximumReadSpeed`, `PriceGB`, `Interface`, `FormFactor`, `MaximumWriteSpeed`, `Size` FROM component INNER JOIN ssd ON component.CompID = ssd.CompID WHERE component.CompPrice <= $Budget AND ssd.Size $symbols[$Loop] $Capacity ORDER BY ssd.Size DESC LIMIT 1", $alts);
                $Loop += 1;
            }
            return $alts;
        }

        public function DBGetPSUAlt($PSUBudget){
            $alts = array();
            $Budgets = $this->Budget($PSUBudget);
            foreach ($Budgets as $Budget) {
                $this->GetAlts("SELECT component.`CompID`, `CompName`, `CompPrice`, `CompLink`, `Capacity`, `Modular`, `TemperatureControlledFan`, `NumberOfFans`, `Format`, `80PlusCertification` FROM component INNER JOIN psu ON component.CompID = psu.CompID WHERE component.CompPrice <= $Budget ORDER BY component.CompPrice DESC LIMIT 1", $alts);
            }
            return $alts;
        }
    }
?>
