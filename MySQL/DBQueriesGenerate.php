<?php
    class DBQueriesGenerate {

        public function Query($query){
            $sql = new DBConnection("compcreator");
            $DBObject = $sql->query("$query");
            $result = $DBObject->fetch_assoc();
            return $result;
        }

        public function DBGetCase($CASEBudget, $FormFactor){
            $rows = array();
            $sql = new DBConnection("compcreator");

            $result = $sql->query("SELECT `CompName`, `CompPrice`, `CompLink`, `TypeOfChassis`, `Material`, `Dimensions`, `ProductPage`, `NumberOfCardSlots`, `35DriveBays`, `ScrewlessDesign`, `Format`, `PositionOfThePowerSupply`, `Volume`, `SupportedMotherboards`, `Colour`, `RoomForExpansion`, `ReleaseYear`, `MaximumLengthOfVideoCard`, `HeightOfExpansionSlots`, `ActiveCooling`, `525DriveBays`, `Weight`, `WaterCooling`, `FanSpacesTotal`, `MaximumMotherboardSize`, `25DriveBays`, `MaxCPUCoolerHeight`, `BuiltInWatercooling`, `FrontConnections` FROM component INNER JOIN systemcase ON component.CompID = systemcase.CompID WHERE component.CompPrice <= $CASEBudget AND systemcase.TypeOfChassis = '$FormFactor' AND NOT systemcase.SupportedMotherboards = '0' ORDER BY component.CompPrice DESC LIMIT 1");
            while($row = mysqli_fetch_assoc($result)){
                foreach ($row as $col => $value) {
                    $rows[$col]= $value;
                }
            }
            if (isset($rows['CompName']) && $rows['CompName'] != "") {
                return $rows;
            }
        }

        public function DBGetMOBO($MOBOBudget, $supportedMOBOFormats, $WIFI){
            $rows = array();
            unset($MOBOFormatsQuery);
            foreach($supportedMOBOFormats as $Format){
                $Format = ltrim($Format);
                if (isset($MOBOFormatsQuery)){
                    $MOBOFormatsQuery = $MOBOFormatsQuery . " OR motherboard.FormFactor = '$Format'";
                }else{
                    $MOBOFormatsQuery = "motherboard.FormFactor = '$Format'";
                }
            }

            unset($WIFIQuery);
            if($WIFI == "Yes"){
                $WIFIQuery = " AND motherboard.WirelessNetwork = 'Yes'";
            }else{
                $WIFIQuery = "";
            }
            $sql = new DBConnection("compcreator");

            $result = $sql->query("SELECT `CompName`, `CompPrice`, `CompLink`, `Width`, `Cooling`, `FormFactor`, `PCISlots`, `NumberOfHdmiOutputs`, `EthernetConnection`, `Socket`, `HDMI`, `HeadPhoneOutput`, `TypeOfMemory`, `USB2`, `mSATA`, `Bluetooth`, `ECCSupport`, `PCIExpressx8`, `TypeOfRaid`, `PCIExpressx1`, `PCIExpressx4`, `ManufacturerWarranty`, `PCIExpressVersion`, `SoundCard`, `PCIExpressx16`, `MemorySpeeds`, `ProductPage`, `ChassisFanConnectors`, `PCIExpressMini`, `BluetoothVersion`, `MemorySlots`, `MiniPCI`, `DVI`, `SupportForMultipleGraphicsCards`, `SupportForIIntegratedGraphicsInCPU`, `SATAExpress`, `MicrophoneInput`, `NumberOfEthernetConnections`, `PowerFanConnector`, `NumberOfDisplayportOutputs`, `Chipset`, `SATA3Gbs`, `MaximumAmountOfMemory`, `Displayport`, `USB`, `SoundCardChip`, `RaidController`, `Thunderbolt`, `64bitProcessor`, `U2`, `Depth`, `ReleaseYear`, `M2`, `VGAOutputs`, `WirelessNetwork`, `SATA6Gbs` FROM component INNER JOIN motherboard ON component.CompID = motherboard.CompID WHERE component.CompPrice <= $MOBOBudget AND (" . $MOBOFormatsQuery . ")" . $WIFIQuery . " ORDER BY component.CompPrice DESC LIMIT 1");
            while($row = mysqli_fetch_assoc($result)){
                foreach ($row as $col => $value) {
                    $rows[$col]= $value;
                }
            }
            if (isset($rows['CompName']) && $rows['CompName'] != "") {
                return $rows;
            }
        }

        public function DBGetCPU($CPUBudget, $Socket){
            $rows = array();
            $sql = new DBConnection("compcreator");
            $result = $sql->query("SELECT `CompName`, `CompPrice`, `CompLink`, `ClockFrequency`, `ProductPage`, `L3Cache`, `TurboBoostCore`, `CPUType`, `BoxVersion`, `NumberOfThreads`, `64bitProcessor`, `L2Cache`, `Socket`, `NumberOfCores`, `GraphicsProcessor`, `ReleaseYear`, `ThermalDesignPower`, `IntegratedGraphics`, `Virtualization`, `CPURating` FROM component INNER JOIN cpu ON component.CompID = cpu.CompID WHERE component.CompPrice <= $CPUBudget AND cpu.Socket = '$Socket' ORDER BY cpu.CPURating ASC LIMIT 1");
            while($row = mysqli_fetch_assoc($result)){
                foreach ($row as $col => $value) {
                    $rows[$col]= $value;
                }
            }
            if (isset($rows['CompName']) && $rows['CompName'] != "") {
                return $rows;
            }
        }

        public function DBGetGPU($GPUBudget){
            $rows = array();
            $sql = new DBConnection("compcreator");
            $result = $sql->query("SELECT `CompName`, `CompPrice`, `CompLink`, `Cooling`, `NumberOfFans`, `SemiPassive`, `FactoryOverclocked`, `GraphicsProcessor`, `LowProfile`, `NonreferenceCooler`, `PCIExpressVersion`, `Displayport`, `NumberOfDisplayportoutputs`, `DVI`, `NumberOfDVIOutputs`, `HDMI`, `NumberOfHdmiOutputs`, `VGAOutputs`, `MaximumResolution`, `NumberOfSupportedMonitors`, `Length`, `NumberOfSlots`, `DirectX`, `HDR`, `OpenGL`, `Vulkan`, `SupportForMultipleGraphicsCards`, `MemoryBandwidth`, `MemoryCapacity`, `MemoryInterface`, `MemorySpeed`, `MemoryType`, `GPUBoost`, `ProcessorSpeed`, `SupplementaryPowerConnector`, `ManufacturerWarranty`, `ReleaseYear`, `ProductPage`, `GPURating` FROM component INNER JOIN gpu ON component.CompID = gpu.CompID WHERE component.CompPrice <= $GPUBudget ORDER BY gpu.GPURating ASC LIMIT 1");
            while($row = mysqli_fetch_assoc($result)){
                foreach ($row as $col => $value) {
                    $rows[$col]= $value;
                }
            }
            if (isset($rows['CompName']) && $rows['CompName'] != "") {
                return $rows;
            }
        }

        public function DBGetHDD($HDDBudget){
            $rows = array();
            $sql = new DBConnection("compcreator");
            $result = $sql->query("SELECT `CompName`, `CompPrice`, `CompLink`, `InternalTransferRate`, `ProductPage`, `HybridDisk`, `PriceperTeraByte`, `FormFactor`, `ManufacturerWarranty`, `Interface`, `CacheSize`, `Connection`, `ReleaseYear`, `RotationalSpeed`, `NoiseLevel`, `HardDriveSize` FROM component INNER JOIN hdd ON component.CompID = hdd.CompID WHERE component.CompPrice <= $HDDBudget ORDER BY hdd.Harddrivesize DESC LIMIT 1");
            while($row = mysqli_fetch_assoc($result)){
                foreach ($row as $col => $value) {
                    $rows[$col]= $value;
                }
            }
            if (isset($rows['CompName']) && $rows['CompName'] != "") {
                return $rows;
            }
        }

        public function DBGetSSD($SSDBudget){
            $rows = array();
            $sql = new DBConnection("compcreator");
            $result = $sql->query("SELECT `CompName`, `CompPrice`, `CompLink`,  `MaximumReadSpeed`, `ControllerChip`, `PricePerGigabyte`, `ManufacturerWarranty`, `Interface`, `FormFactor`, `TypeOfFlashMemory`, `Connection`, `ProductPage`, `Weight`, `MaximumWriteSpeed`, `Size` FROM component INNER JOIN ssd ON component.CompID = ssd.CompID WHERE component.CompPrice <= $SSDBudget ORDER BY ssd.Size DESC LIMIT 1");
            while($row = mysqli_fetch_assoc($result)){
                foreach ($row as $col => $value) {
                    $rows[$col]= $value;
                }
            }
            if (isset($rows['CompName']) && $rows['CompName'] != "") {
                return $rows;
            }
        }

        public function DBGetRAM($RAMBudget, $Typeofmemory, $Memoryslots){
            $rows = array();
            $sql = new DBConnection("compcreator");
            $result = $sql->query("SELECT `CompName`, `CompPrice`, `CompLink`, `NumberOfModules`, `MemorySpeed`, `MemoryCapacity`, `ECC`, `ReleaseYear`, `PricePerGigabyte`, `ManufacturerWarranty`, `MemoryCapacityPerModule`, `CASLatency`, `TypeOfMemory`, `ProductPage`, `Voltage` FROM component INNER JOIN memory ON component.CompID = memory.CompID WHERE component.CompPrice < $RAMBudget AND memory.TypeOfMemory = '$Typeofmemory' AND memory.NumberOfModules <= $Memoryslots ORDER BY memory.MemoryCapacity DESC, memory.PricePerGigabyte LIMIT 1");
            while($row = mysqli_fetch_assoc($result)){
                foreach ($row as $col => $value) {
                    $rows[$col]= $value;
                }
            }
            if (isset($rows['CompName']) && $rows['CompName'] != "") {
                return $rows;
            }
        }

        public function DBGetPSU($PSUBudget){
            $rows = array();
            $sql = new DBConnection("compcreator");
            $result = $sql->query("SELECT `CompName`, `CompPrice`, `CompLink`, `Capacity`, `ReleaseYear`, `FanSize`, `Modular`, `CableSocks`, `PowerConnectorsForSata`, `PowerConnectionsForPciExpress`, `TemperatureControlledFan`, `ManufacturerWarranty`, `ProductPage`, `Efficiency`, `NumberOfFans`, `Semipassive`, `Format`, `80PlusCertification` FROM component INNER JOIN psu ON component.CompID = psu.CompID WHERE component.CompPrice <= $PSUBudget ORDER BY component.CompPrice DESC LIMIT 1");
            while($row = mysqli_fetch_assoc($result)){
                foreach ($row as $col => $value) {
                    $rows[$col]= $value;
                }
            }
            if (isset($rows['CompName']) && $rows['CompName'] != "") {
                return $rows;
            }
        }
    }
?>
