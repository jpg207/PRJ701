<?php
    include('DBConnection.php');

    try {
        $sql = new DBConnection("compcreator");
        $sql->query("DROP DATABASE IF EXISTS CompCreator;");
        $sql->query("CREATE DATABASE CompCreator CHARACTER SET utf8 COLLATE utf8_unicode_ci;");
        $sql->query("USE CompCreator;");
        $sql->query("DROP TABLE IF EXISTS CPU;");
        $sql->query("DROP TABLE IF EXISTS GPU;");
        $sql->query("DROP TABLE IF EXISTS SystemCase;");
        $sql->query("DROP TABLE IF EXISTS PSU;");
        $sql->query("DROP TABLE IF EXISTS StorageDrive;");
        $sql->query("DROP TABLE IF EXISTS Memory;");
        $sql->query("DROP TABLE IF EXISTS MotherBoard;");
        $sql->query("DROP TABLE IF EXISTS Component;");

		$sql->query("CREATE TABLE Component(
			CompID INT(10) PRIMARY KEY NOT NULL,
			CompName VARCHAR(200) NOT NULL,
			CompPrice DECIMAL(6,2) NOT NULL,
			CompLink VARCHAR(200) NOT NULL,
            CompDate  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP());
        ");

		$sql->query("CREATE TABLE CPU(
			CompID INT (10)PRIMARY KEY NOT NULL,
			ClockFrequency DECIMAL(10,2),
			ProductPage VARCHAR(200),
			L3Cache DECIMAL(10,2),
			TurboBoostCore VARCHAR(200),
			CPUType VARCHAR(200),
			BoxVersion VARCHAR(200),
            Boxed VARCHAR(200),
            ECC VARCHAR(200),
            TechnologyType VARCHAR(200),
			NumberOfThreads INT(8),
            Instructions VARCHAR(200),
            Manual VARCHAR(200),
			64bitProcessor VARCHAR(200),
            Busspeed INT(8),
            TubroFrequency INT(8),
			L2Cache DECIMAL(10,2),
			Socket VARCHAR(200),
			NumberOfCores INT(8),
            CPUthrottling VARCHAR(200),
			GraphicsProcessor VARCHAR(200),
			ReleaseYear INT(8),
			ThermalDesignPower INT(8),
			IntegratedGraphics VARCHAR(200),
			Virtualization VARCHAR(200),
            MicronTechnology VARCHAR(200),
            Frequency INT(200),
            MaxFrequency INT(200),
            Dimensions VARCHAR(200),
            Otherconnectors VARCHAR(200),
			CPURating INT (10) NOT NULL DEFAULT '99999999',
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE );
        ");

		$sql->query("CREATE TABLE GPU(
            CompID INT (10)PRIMARY KEY NOT NULL,
			Cooling VARCHAR(200),
			NumberOfFans INT(8),
			SemiPassive VARCHAR(200),
			FactoryOverclocked VARCHAR(200),
			GraphicsProcessor VARCHAR(200),
			LowProfile VARCHAR(200),
			NonreferenceCooler VARCHAR(200),
			PCIExpressVersion VARCHAR(200),
			Displayport VARCHAR(200),
			NumberOfDisplayportoutputs INT(8),
			DVI VARCHAR(200),
			NumberOfDVIOutputs INT(8),
			HDMI VARCHAR(200),
			NumberOfHdmiOutputs INT(8),
			VGAOutputs VARCHAR(200),
			MaximumResolution VARCHAR(200),
			NumberOfSupportedMonitors INT(8),
			Length INT(8),
			NumberOfSlots INT(8),
			DirectX DECIMAL(10,2),
			HDR VARCHAR(200),
			OpenGL DECIMAL(10,2),
			Vulkan DECIMAL(10,2),
			SupportForMultipleGraphicsCards VARCHAR(200),
			MemoryBandwidth DECIMAL(10,2),
			MemoryCapacity DECIMAL(10,2),
			MemoryInterface DECIMAL(10,2),
			MemorySpeed DECIMAL(10,2),
			MemoryType VARCHAR(200),
			GPUBoost VARCHAR(200),
			ProcessorSpeed DECIMAL(10,2),
			SupplementaryPowerConnector VARCHAR(200),
			ManufacturerWarranty INT(8),
			ReleaseYear INT(8),
			ProductPage VARCHAR(200),
            Otherconnectors VARCHAR(200),
            PCIExpresschannels INT(8),
            UnifiedShadingarchitecture VARCHAR(200),
            UnifiedShaders INT(8),
            CADCAMGraphicsCard VARCHAR(200),
            DualLink VARCHAR(200),
            SingleLink VARCHAR(200),
            DVIHDMIadapterincluded VARCHAR(200),
            HDMIDVIadapterincluded VARCHAR(200),
            DVIVGAadapterincluded VARCHAR(200),
            HDMIversion DECIMAL(8,2),
            TypeofHDMIconnector VARCHAR(200),
            DisplayPortversion DECIMAL(8,2),
            TypeofDisplayPortconnector VARCHAR(200),
            Operatingsystem VARCHAR(200),
            Features VARCHAR(200),
            Memoryacceleration VARCHAR(200),
            MultiGPUTechnology VARCHAR(200),
            Dimensions VARCHAR(200),
            Fibre VARCHAR(200),
			GPURating INT (10) NOT NULL DEFAULT '99999999',
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);
        ");

		$sql->query("CREATE TABLE SystemCase(
			CompID INT (10)PRIMARY KEY NOT NULL,
            Material VARCHAR(200),
            Dimensions VARCHAR(200),
            No VARCHAR(200),
            FanSpaces220230mm INT(8),
            ProductPage VARCHAR(200),
            NumberOfCardSlots INT(8),
            35DriveBays INT(8),
            FanSpaces120mm INT(8),
            ScrewlessDesign VARCHAR(200),
            FanSpaces9092mm INT(8),
            FanSpaces400mm INT(8),
            FanSpaces4050mm INT(8),
            HeatZones INT(8),
            Format VARCHAR(200),
            FanSpaces180190mm INT(8),
            PSUIncluded VARCHAR(200),
            FanSpaces6070mm INT(8),
            PositionOfThePowerSupply INT(8),
            Volume VARCHAR(200),
            FanSpaces200mm INT(8),
            SupportedMotherboards VARCHAR(200),
            FanSpaces330mm INT(8),
            FanSpaces140mm INT(8),
            BuiltinWatercooling VARCHAR(200),
            FanSpaces80mm INT(8),
            Colour VARCHAR(200),
            Manual VARCHAR(200),
            RoomForExpansion VARCHAR(200),
            SlimDiskStation VARCHAR(200),
            ReleaseYear INT(8),
            MaximumLengthOfVideoCard INT(8),
            HeightOfExpansionSlots INT(8),
            ActiveCooling VARCHAR(200),
            Features VARCHAR(200),
            525DriveBays INT(8),
            Weight INT(8),
            WaterCooling VARCHAR(200),
            FanSpacesTotal INT(8),
            SlimOpticalDrive VARCHAR(200),
            MaximumMotherboardSize VARCHAR(200),
            25DriveBays INT(8),
            MaxCPUCoolerHeight INT(8),
            FanSpaces250mm INT(8),
            TypeOfChassis VARCHAR(200),
            FrontConnections VARCHAR(200),
            BuiltInMicrophone VARCHAR(200),
            PictBridge VARCHAR(200),
            GPRS VARCHAR(200),
            OtherConnectors VARCHAR(200),
            PaperStorage VARCHAR(200),
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);
        ");

		$sql->query("CREATE TABLE PSU(
			CompID INT (10)PRIMARY KEY NOT NULL,
            Capacity INT(8),
            Dimensions VARCHAR(200),
            PowerConnectionsForThe35 VARCHAR(200),
            ProductPage VARCHAR(200),
            80PlusCertification VARCHAR(200),
            FanConnection VARCHAR(200),
            ATX12V VARCHAR(200),
            FanSize INT(8),
            Modular VARCHAR(200),
            Number12VLines INT(8),
            CableSocks VARCHAR(200),
            ATX VARCHAR(200),
            PowerConnectorsForSATA VARCHAR(200),
            Current12V8 INT(8),
            Current12V7 INT(8),
            Current12V6 INT(8),
            Current12V5 INT(8),
            Current12V4 INT(8),
            Current12V3 INT(8),
            Current12V2 INT(8),
            Current12V1 INT(8),
            Efficiency INT(8),
            ManufacturerWarranty VARCHAR(200),
            PowerSupplyUnitPSU VARCHAR(200),
            SemiPassive VARCHAR(200),
            TemperatureControlledFan VARCHAR(200),
            Current5Vsb INT(8),
            Manual VARCHAR(200),
            NumberOfFans VARCHAR(200),
            Current5V INT(8),
            Current12V INT(8),
            ReleaseYear INT(8),
            Current33V INT(8),
            PowerConnectionsForTheFloppyDrive VARCHAR(200),
            ActiveCooling VARCHAR(200),
            Features VARCHAR(200),
            Format VARCHAR(200),
            BearingType VARCHAR(200),
            Version VARCHAR(200),
            EPS12V VARCHAR(200),
            PowerConnectionsForPCIExpress VARCHAR(200),
            TotalAmperage12V INT(8),
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);
        ");

		$sql->query("CREATE TABLE SSD(
			CompID INT (10)PRIMARY KEY NOT NULL,
			MaximumReadSpeed INT(8),
			ControllerChip VARCHAR(200),
			PriceGB DECIMAL(10,2),
			ManufacturerWarranty INT(8),
			Interface VARCHAR(200),
			FormFactor VARCHAR(200),
			TypeOfFlashMemory VARCHAR(200),
			Connection VARCHAR(200),
			ProductPage VARCHAR(200),
			Weight VARCHAR(200),
			MaximumWriteSpeed INT(8),
            4KBRandomRead VARCHAR(200),
            Dimensions VARCHAR(200),
            4KBRandomWrite VARCHAR(200),
            Cachesize INT(8),
            Cachememory VARCHAR(200),
            Powerconsumption INT(8),
            OperationalPowerConsumption INT(8),
            TBW VARCHAR(200),
            Hybriddisk VARCHAR(200),
            Manual VARCHAR(200),
            Internalconnection VARCHAR(200),
            Externalconnection VARCHAR(200),
            Features VARCHAR(200),
            M2formfactor VARCHAR(200),
            Firewire VARCHAR(200),
			Size INT(8),
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);
        ");

		$sql->query("CREATE TABLE HDD(
			CompID INT (10)PRIMARY KEY NOT NULL,
			InternalTransferRate INT(8),
			ProductPage VARCHAR(200),
			HybridDisk VARCHAR(200),
			PriceTB DECIMAL(10,2),
			FormFactor VARCHAR(200),
			ManufacturerWarranty INT(8),
			Interface VARCHAR(200),
			CacheSize INT(8),
			Connection VARCHAR(200),
			ReleaseYear INT(8),
			RotationalSpeed VARCHAR(200),
			NoiseLevel VARCHAR(200),
			HardDriveSize DECIMAL(10,2),
            Dimensions VARCHAR(200),
            MeanTimeBetweenFailure VARCHAR(200),
            Averagesearch  VARCHAR(200),
            UnrecoverableReadError VARCHAR(200),
            Numberofdisks INT(8),
            Manual VARCHAR(200),
            Features VARCHAR(200),
            AdvancedFormat VARCHAR(200),
            OperationalPowerConsumption INT(8),
            Powerconsumption INT(8),
            FlashMemory VARCHAR(200),
            Fibre VARCHAR(200),
            GPRS VARCHAR(200),
            USB VARCHAR(200),
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);
        ");

		$sql->query("CREATE TABLE Memory(
			CompID INT (10)PRIMARY KEY NOT NULL,
			NumberOfModules INT(8),
			MemorySpeed VARCHAR(200),
			MemoryCapacity INT(8),
			ECC VARCHAR(200),
			ReleaseYear INT(8),
			PriceGB DECIMAL(10,2),
			ManufacturerWarranty VARCHAR(200),
			MemoryCapacityPerModule INT(8),
			CASLatency INT(8),
			TypeOfMemory VARCHAR(200),
			ProductPage VARCHAR(200),
			Voltage VARCHAR(200),
            Features VARCHAR(200),
            Registered VARCHAR(200),
            Cooling VARCHAR(200),
            IntelXMP VARCHAR(200),
            Timing VARCHAR(200),
            Manual VARCHAR(200),
            Paperstorage VARCHAR(200),
            Weight INT(8),
            Capacity INT(8),
            M2key VARCHAR(200),
            USBversion VARCHAR(200),
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);
        ");

		$sql->query("CREATE TABLE MotherBoard(
			CompID INT (10)PRIMARY KEY NOT NULL,
            InternalSerialPort VARCHAR(200),
            CompositeOutput VARCHAR(200),
            InternalThunderbolt VARCHAR(200),
            Width VARCHAR(200),
            Cooling VARCHAR(200),
            InternalUSB20 VARCHAR(200),
            FormFactor VARCHAR(200),
            NumberOfProcessorSockets VARCHAR(200),
            PCIslots VARCHAR(200),
            NumberOfHDMIOutputs VARCHAR(200),
            IPMI VARCHAR(200),
            SAS VARCHAR(200),
            EthernetConnection VARCHAR(200),
            Socket VARCHAR(200),
            HDMI VARCHAR(200),
            InternalParallelPort VARCHAR(200),
            TPM VARCHAR(200),
            NumberOf10100100010000MbitsPort VARCHAR(200),
            HeadphoneOutput VARCHAR(200),
            NetworkChip VARCHAR(200),
            TypeOfMemory VARCHAR(200),
            InternalUSB3031 VARCHAR(200),
            USB20 VARCHAR(200),
            eSATA VARCHAR(200),
            NumberOf101001000Mbitsports VARCHAR(200),
            mSATA VARCHAR(200),
            Bluetooth VARCHAR(200),
            Connections VARCHAR(200),
            ECCSupport VARCHAR(200),
            Firewire VARCHAR(200),
            PCIExpressx8 VARCHAR(200),
            TypeOfRAID VARCHAR(200),
            PCIExpressx1 VARCHAR(200),
            PCIExpressx4 VARCHAR(200),
            Graphicscard VARCHAR(200),
            ManufacturerWarranty VARCHAR(200),
            SCSIcontroller VARCHAR(200),
            PCIExpressVersion VARCHAR(200),
            SoundCard VARCHAR(200),
            PCIExpressx16 VARCHAR(200),
            MemorySpeeds VARCHAR(200),
            Manual VARCHAR(200),
            SATA15Gbs VARCHAR(200),
            USB31 VARCHAR(200),
            InternalUSB VARCHAR(200),
            ProductPage VARCHAR(200),
            ChassisFanConnectors VARCHAR(200),
            SPDIF VARCHAR(200),
            PCIExpressMini VARCHAR(200),
            BluetoothVersion VARCHAR(200),
            MemorySlots INT(2),
            MiniPCI VARCHAR(200),
            DVI VARCHAR(200),
            ComponentOutput VARCHAR(200),
            SupportForMultipleGraphicscards VARCHAR(200),
            IDEPATA VARCHAR(200),
            SupportForIntegratedGraphicsInCPU VARCHAR(200),
            AGPSlot VARCHAR(200),
            SATAExpress VARCHAR(200),
            MicrophoneInput VARCHAR(200),
            PS2 VARCHAR(200),
            NumberOfEthernetConnections VARCHAR(200),
            ParallelPort VARCHAR(200),
            NumberOfDisplayPortOutputs VARCHAR(200),
            NumberOf10100MbitsPorts VARCHAR(200),
            Chipset VARCHAR(200),
            Features VARCHAR(200),
            SerialPort VARCHAR(200),
            SATA3Gbs VARCHAR(200),
            MaximumAmountOfMemory VARCHAR(200),
            InternalUSB31 VARCHAR(200),
            DisplayPort VARCHAR(200),
            64bitProcessor VARCHAR(200),
            USB VARCHAR(200),
            SoundCardChip VARCHAR(200),
            DisplayPortVersion VARCHAR(200),
            RAIDController VARCHAR(200),
            USB3031 VARCHAR(200),
            Thunderbolt VARCHAR(200),
            CompatibleProcessors VARCHAR(200),
            HDMIVersion VARCHAR(200),
            U2 VARCHAR(200),
            Depth VARCHAR(200),
            ReleaseYear VARCHAR(200),
            InternalSPDIF VARCHAR(200),
            WirelessNetworkingStandard VARCHAR(200),
            TypeOfUSBConnector VARCHAR(200),
            M2 VARCHAR(200),
            PCIX VARCHAR(200),
            SATA VARCHAR(200),
            VGAOutputs VARCHAR(200),
            WirelessNetwork VARCHAR(200),
            SATA6Gbs VARCHAR(200),
            NumberOfDVIoutputs VARCHAR(200),
            CoaxialSPDIFout VARCHAR(200),
            PictBridge VARCHAR(200),
            GraphicsProcessor VARCHAR(200),
            MultiGPUTechnology VARCHAR(200),
            DVIHDMIAdapterIncluded VARCHAR(200),
            OpticalSPDIFout VARCHAR(200),
            NumberofThunderboltports VARCHAR(200),
            Firewireversion VARCHAR(200),
            Fibre VARCHAR(200),
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);
        ");

        $sql->query("CREATE TABLE ODD(
            CompID INT (10)PRIMARY KEY NOT NULL,
            TypeOfDrive VARCHAR(200),
            Dimensions VARCHAR(200),
            AvailableColours VARCHAR(200),
            DVD VARCHAR(200),
            AccessTime INT(8),
            Platform VARCHAR(200),
            Location VARCHAR(200),
            LabellingTechnology VARCHAR(200),
            WriteSpeed INT(8),
            Bluray VARCHAR(200),
            CacheSize INT(8),
            SlotInTray VARCHAR(200),
            BurnsDVD VARCHAR(200),
            SupportsMDisc VARCHAR(200),
            Manual VARCHAR(200),
            CD VARCHAR(200),
            Connection VARCHAR(200),
            ProductPage VARCHAR(200),
            Weight INT(8),
            BurnsCD VARCHAR(200),
            ReadSpeed INT(8),
            BurnsBluray VARCHAR(200),
            FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);
        ");

        $sql->query("CREATE TABLE AirCooler(
            CompID INT (10)PRIMARY KEY NOT NULL,
            Fancontrol VARCHAR(200),
            Airflow INT(8),
            Material VARCHAR(200),
            Dimensions VARCHAR(200),
            Weight INT(8),
            NumberOfFansIncluded INT(8),
            ThermalDesignPower INT(8),
            Noiselevel VARCHAR(200),
            ThermalResistance VARCHAR(200),
            Manual VARCHAR(200),
            LightingEffects VARCHAR(200),
            Fansize INT(8),
            ProductPage VARCHAR(200),
            HeatPipes INT(8),
            RotationalSpeed INT(8),
            ActiveCooling VARCHAR(200),
            Socket VARCHAR(200),
            FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);
        ");

        echo("Build complete");
        }catch(Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
?>
