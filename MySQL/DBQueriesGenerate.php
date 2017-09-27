<?php
    class DBQueriesGenerate {

        public function Query($query){
            $sql = new DBConnection("compcreator");
            $DBObject = $sql->query("$query");
            $result = $DBObject->fetch_assoc();
            return $result;
        }

        public function GetItem($Query){
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
            }
        }

        public function DBGetCase($CASEBudget, $FormFactor){
            $rows = $this->GetItem("SELECT component.`CompID`, `CompName`, `CompPrice`, `CompLink`, `Material`, `Dimensions`, `No`, `FanSpaces220230mm`, `ProductPage`, `NumberOfCardSlots`, `35DriveBays`, `FanSpaces120mm`, `ScrewlessDesign`, `FanSpaces9092mm`, `FanSpaces400mm`, `FanSpaces4050mm`, `HeatZones`, `Format`, `FanSpaces180190mm`, `PSUIncluded`, `FanSpaces6070mm`, `PositionOfThePowerSupply`, `Volume`, `FanSpaces200mm`, `SupportedMotherboards`, `FanSpaces330mm`, `FanSpaces140mm`, `BuiltinWatercooling`, `FanSpaces80mm`, `Colour`, `Manual`, `RoomForExpansion`, `SlimDiskStation`, `ReleaseYear`, `MaximumLengthOfVideoCard`, `HeightOfExpansionSlots`, `ActiveCooling`, `Features`, `525DriveBays`, `Weight`, `WaterCooling`, `FanSpacesTotal`, `SlimOpticalDrive`, `MaximumMotherboardSize`, `25DriveBays`, `MaxCPUCoolerHeight`, `FanSpaces250mm`, `TypeOfChassis`, `FrontConnections`, `BuiltInMicrophone`, `PictBridge`, `GPRS`, `OtherConnectors`, `PaperStorage` FROM component INNER JOIN systemcase ON component.CompID = systemcase.CompID WHERE component.CompPrice <= $CASEBudget AND systemcase.TypeOfChassis = '$FormFactor' AND NOT systemcase.SupportedMotherboards = '0' AND systemcase.MaximumLengthOfVideoCard != '0' ORDER BY component.CompPrice DESC LIMIT 1");
            return $rows;
        }

        public function DBGetMOBO($MOBOBudget, $supportedMOBOFormats, $WIFI){
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
            $rows = $this->GetItem("SELECT component.`CompID`, `CompName`, `CompPrice`, `CompLink`, `InternalSerialPort`, `CompositeOutput`, `InternalThunderbolt`, `Width`, `Cooling`, `InternalUSB20`, `FormFactor`, `NumberOfProcessorSockets`, `PCIslots`, `NumberOfHDMIOutputs`, `IPMI`, `SAS`, `EthernetConnection`, `Socket`, `HDMI`, `InternalParallelPort`, `TPM`, `NumberOf10100100010000MbitsPort`, `HeadphoneOutput`, `NetworkChip`, `TypeOfMemory`, `InternalUSB3031`, `USB20`, `eSATA`, `NumberOf101001000Mbitsports`, `mSATA`, `Bluetooth`, `Connections`, `ECCSupport`, `Firewire`, `PCIExpressx8`, `TypeOfRAID`, `PCIExpressx1`, `PCIExpressx4`, `Graphicscard`, `ManufacturerWarranty`, `SCSIcontroller`, `PCIExpressVersion`, `SoundCard`, `PCIExpressx16`, `MemorySpeeds`, `Manual`, `SATA15Gbs`, `USB31`, `InternalUSB`, `ProductPage`, `ChassisFanConnectors`, `SPDIF`, `PCIExpressMini`, `BluetoothVersion`, `MemorySlots`, `MiniPCI`, `DVI`, `ComponentOutput`, `SupportForMultipleGraphicscards`, `IDEPATA`, `SupportForIntegratedGraphicsInCPU`, `AGPSlot`, `SATAExpress`, `MicrophoneInput`, `PS2`, `NumberOfEthernetConnections`, `ParallelPort`, `NumberOfDisplayPortOutputs`, `NumberOf10100MbitsPorts`, `Chipset`, `Features`, `SerialPort`, `SATA3Gbs`, `MaximumAmountOfMemory`, `InternalUSB31`, `DisplayPort`, `64bitProcessor`, `USB`, `SoundCardChip`, `DisplayPortVersion`, `RAIDController`, `USB3031`, `Thunderbolt`, `CompatibleProcessors`, `HDMIVersion`, `U2`, `Depth`, `ReleaseYear`, `InternalSPDIF`, `WirelessNetworkingStandard`, `TypeOfUSBConnector`, `M2`, `PCIX`, `SATA`, `VGAOutputs`, `WirelessNetwork`, `SATA6Gbs`, `NumberOfDVIoutputs`, `CoaxialSPDIFout`, `PictBridge`, `GraphicsProcessor`, `MultiGPUTechnology`, `DVIHDMIAdapterIncluded`, `OpticalSPDIFout`, `NumberofThunderboltports`, `Firewireversion`, `Fibre` FROM component INNER JOIN motherboard ON component.CompID = motherboard.CompID WHERE component.CompPrice <= $MOBOBudget AND (" . $MOBOFormatsQuery . ")" . $WIFIQuery . " ORDER BY component.CompPrice DESC LIMIT 1");
            return $rows;
        }

        public function DBGetCPU($CPUBudget, $Socket){
            $rows = $this->GetItem("SELECT component.`CompID`, `CompName`, `CompPrice`, `CompLink`, `ClockFrequency`, `ProductPage`, `L3Cache`, `TurboBoostCore`, `CPUType`, `BoxVersion`, `Boxed`, `ECC`, `TechnologyType`, `NumberOfThreads`, `Instructions`, `Manual`, `64bitProcessor`, `Busspeed`, `TubroFrequency`, `L2Cache`, `Socket`, `NumberOfCores`, `CPUthrottling`, `GraphicsProcessor`, `ReleaseYear`, `ThermalDesignPower`, `IntegratedGraphics`, `Virtualization`, `MicronTechnology`, `Frequency`, `MaxFrequency`, `Dimensions`, `Otherconnectors`, `CPURating` FROM component INNER JOIN cpu ON component.CompID = cpu.CompID WHERE component.CompPrice <= $CPUBudget AND cpu.Socket = '$Socket' ORDER BY cpu.CPURating ASC LIMIT 1");
            return $rows;
        }

        public function DBGetGPU($GPUBudget, $Length){
            $rows = $this->GetItem("SELECT component.`CompID`, `CompName`, `CompPrice`, `CompLink`, `Cooling`, `NumberOfFans`, `SemiPassive`, `FactoryOverclocked`, `GraphicsProcessor`, `LowProfile`, `NonreferenceCooler`, `PCIExpressVersion`, `Displayport`, `NumberOfDisplayportoutputs`, `DVI`, `NumberOfDVIOutputs`, `HDMI`, `NumberOfHdmiOutputs`, `VGAOutputs`, `MaximumResolution`, `NumberOfSupportedMonitors`, `Length`, `NumberOfSlots`, `DirectX`, `HDR`, `OpenGL`, `Vulkan`, `SupportForMultipleGraphicsCards`, `MemoryBandwidth`, `MemoryCapacity`, `MemoryInterface`, `MemorySpeed`, `MemoryType`, `GPUBoost`, `ProcessorSpeed`, `SupplementaryPowerConnector`, `ManufacturerWarranty`, `ReleaseYear`, `ProductPage`, `Otherconnectors`, `PCIExpresschannels`, `UnifiedShadingarchitecture`, `UnifiedShaders`, `CADCAMGraphicsCard`, `DualLink`, `SingleLink`, `DVIHDMIadapterincluded`, `HDMIDVIadapterincluded`, `DVIVGAadapterincluded`, `HDMIversion`, `TypeofHDMIconnector`, `DisplayPortversion`, `TypeofDisplayPortconnector`, `Operatingsystem`, `Features`, `Memoryacceleration`, `MultiGPUTechnology`, `Dimensions`, `Fibre`, `GPURating` FROM component INNER JOIN gpu ON component.CompID = gpu.CompID WHERE component.CompPrice <= $GPUBudget AND gpu.Length <= $Length ORDER BY gpu.GPURating ASC LIMIT 1");
            return $rows;
        }

        public function DBGetHDD($HDDBudget){
            $rows = $this->GetItem("SELECT component.`CompID`, `CompName`, `CompPrice`, `CompLink`, `InternalTransferRate`, `ProductPage`, `HybridDisk`, `PriceTB`, `FormFactor`, `ManufacturerWarranty`, `Interface`, `CacheSize`, `Connection`, `ReleaseYear`, `RotationalSpeed`, `NoiseLevel`, `HardDriveSize`, `Dimensions`, `MeanTimeBetweenFailure`, `Averagesearch`, `UnrecoverableReadError`, `Numberofdisks`, `Manual`, `Features`, `AdvancedFormat`, `OperationalPowerConsumption`, `Powerconsumption`, `FlashMemory`, `Fibre`, `GPRS`, `USB` FROM component INNER JOIN hdd ON component.CompID = hdd.CompID WHERE component.CompPrice <= $HDDBudget ORDER BY hdd.Harddrivesize DESC LIMIT 1");
            return $rows;
        }

        public function DBGetSSD($SSDBudget){
            $rows = $this->GetItem("SELECT component.`CompID`, `CompName`, `CompPrice`, `CompLink`, `MaximumReadSpeed`, `ControllerChip`, `PriceGB`, `ManufacturerWarranty`, `Interface`, `FormFactor`, `TypeOfFlashMemory`, `Connection`, `ProductPage`, `Weight`, `MaximumWriteSpeed`, `4KBRandomRead`, `Dimensions`, `4KBRandomWrite`, `Cachesize`, `Cachememory`, `Powerconsumption`, `OperationalPowerConsumption`, `TBW`, `Hybriddisk`, `Manual`, `Internalconnection`, `Externalconnection`, `Features`, `M2formfactor`, `Firewire`, `Size` FROM component INNER JOIN ssd ON component.CompID = ssd.CompID WHERE component.CompPrice <= $SSDBudget ORDER BY ssd.Size DESC LIMIT 1");
            return $rows;
        }

        public function DBGetRAM($RAMBudget, $Typeofmemory, $Memoryslots){
            $rows = $this->GetItem("SELECT component.`CompID`, `CompName`, `CompPrice`, `CompLink`, `NumberOfModules`, `MemorySpeed`, `MemoryCapacity`, `ECC`, `ReleaseYear`, `PriceGB`, `ManufacturerWarranty`, `MemoryCapacityPerModule`, `CASLatency`, `TypeOfMemory`, `ProductPage`, `Voltage`, `Features`, `Registered`, `Cooling`, `IntelXMP`, `Timing`, `Manual`, `Paperstorage`, `Weight`, `Capacity`, `M2key`, `USBversion` FROM component INNER JOIN memory ON component.CompID = memory.CompID WHERE component.CompPrice < $RAMBudget AND memory.TypeOfMemory = '$Typeofmemory' AND memory.NumberOfModules <= $Memoryslots ORDER BY memory.MemoryCapacity DESC, memory.PriceGB LIMIT 1");
            return $rows;
        }

        public function DBGetPSU($PSUBudget){
            $rows = $this->GetItem("SELECT component.`CompID`, `CompName`, `CompPrice`, `CompLink`, `Capacity`, `Dimensions`, `PowerConnectionsforthe35`, `Productpage`, `80pluscertification`, `Fanconnection`, `ATX12V`, `Fansize`, `Modular`, `Number12Vlines`, `Cablesocks`, `ATX`, `PowerconnectorsforSATA`, `Current12V8`, `Current12V7`, `Current12V6`, `Current12V5`, `Current12V4`, `Current12V3`, `Current12V2`, `Current12V1`, `Efficiency`, `Manufacturerwarranty`, `PowersupplyunitPSU`, `Semipassive`, `Temperaturecontrolledfan`, `Current5Vsb`, `Manual`, `Numberoffans`, `Current5V`, `Current12V`, `Releaseyear`, `Current33V`, `Powerconnectionsforthefloppydrive`, `Activecooling`, `Features`, `Format`, `Bearingtype`, `Version`, `EPS12V`, `PowerconnectionsforPCIExpress`, `Totalamperage12V`FROM component INNER JOIN psu ON component.CompID = psu.CompID WHERE component.CompPrice <= $PSUBudget ORDER BY component.CompPrice DESC LIMIT 1");
            return $rows;
        }
    }
?>
