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
			CompID INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
			CompName VARCHAR(200) NOT NULL,
			CompPrice DECIMAL(6,2) NOT NULL,
			CompLink VARCHAR(200) NOT NULL );");

		  $sql->query("CREATE TABLE CPU(
			CPUID INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
			ClockFrequency DECIMAL(10,2),
			ProductPage VARCHAR(200),
			L3Cache DECIMAL(10,2),
			TurboBoostCore VARCHAR(200),
			CPUType VARCHAR(200),
			BoxVersion VARCHAR(200),
			NumberOfThreads INT(8),
			64bitProcessor VARCHAR(200),
			L2Cache DECIMAL(10,2),
			Socket VARCHAR(200),
			NumberOfCores INT(8),
			GraphicsProcessor VARCHAR(200),
			ReleaseYear INT(8),
			ThermalDesignPower INT(8),
			IntegratedGraphics VARCHAR(200),
			Virtualization VARCHAR(200),
			CPURating DECIMAL (10, 2) NOT NULL,
			CompID INT (10) NOT NULL,
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE );");

		  $sql->query("CREATE TABLE GPU(
			GPUID INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
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
			Length VARCHAR(200),
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
			GPURating DECIMAL (10,2),
			CompID INT (10) NOT NULL,
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);");

		  $sql->query("CREATE TABLE SystemCase(
			CaseID INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
			TypeOfChassis VARCHAR(200),
			Material VARCHAR(200),
			Dimensions VARCHAR(200),
			ProductPage VARCHAR(200),
			NumberOfCardSlots INT(8),
			35DriveBays INT(8),
			ScrewlessDesign VARCHAR(200),
			Format VARCHAR(200),
			PositionOfThePowerSupply VARCHAR(200),
			Volume VARCHAR(200),
			SupportedMotherboards VARCHAR(200),
			Colour VARCHAR(200),
			RoomForExpansion VARCHAR(200),
			ReleaseYear INT(8),
			MaximumLengthOfVideoCard DECIMAL(10,2),
			HeightOfExpansionSlots VARCHAR(200),
			ActiveCooling VARCHAR(200),
			525DriveBays INT(8),
			Weight VARCHAR(200),
			WaterCooling VARCHAR(200),
			FanSpacesTotal VARCHAR(200),
			MaximumMotherboardSize VARCHAR(200),
			25DriveBays INT(8),
			MaxCPUcoolerheight DECIMAL(10,2),
			BuiltInWatercooling VARCHAR(200),
			FrontConnections VARCHAR(200),
			CompID INT (10) NOT NULL,
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);");

		  $sql->query("CREATE TABLE PSU(
			PSUID INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
			Capacity  INT(8),
			ReleaseYear INT(8),
			FanSize INT(8),
			Modular VARCHAR(200),
			CableSocks VARCHAR(200),
			PowerConnectorsForSata INT(8),
			PowerConnectionsForPciExpress INT(8),
			TemperatureControlledFan VARCHAR(200),
			ManufacturerWarranty INT(8),
			ProductPage VARCHAR(200),
			Efficiency INT(8),
			NumberOfFans INT(8),
			Semipassive VARCHAR(200),
			Format VARCHAR(200),
			80PlusCertification VARCHAR(200),
			CompID INT (10) NOT NULL,
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);");

		  $sql->query("CREATE TABLE SSD(
			SSDID INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
			MaximumReadSpeed INT(8),
			ControllerChip VARCHAR(200),
			PricePerGigabyte DECIMAL(10,2),
			ManufacturerWarranty INT(8),
			Interface VARCHAR(200),
			FormFactor VARCHAR(200),
			TypeOfFlashMemory VARCHAR(200),
			Connection VARCHAR(200),
			ProductPage VARCHAR(200),
			Weight VARCHAR(200),
			MaximumWriteSpeed INT(8),
			Size INT(8),
			CompID INT (10) NOT NULL,
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);");

		  $sql->query("CREATE TABLE HDD(
			HDDID INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
			InternalTransferRate INT(8),
			ProductPage VARCHAR(200),
			HybridDisk VARCHAR(200),
			PriceperTeraByte DECIMAL(10,2),
			FormFactor VARCHAR(200),
			ManufacturerWarranty INT(8),
			Interface VARCHAR(200),
			CacheSize INT(8),
			Connection VARCHAR(200),
			ReleaseYear INT(8),
			RotationalSpeed VARCHAR(200),
			NoiseLevel VARCHAR(200),
			HardDriveSize DECIMAL(10,2),
			CompID INT (10) NOT NULL,
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);");

		  $sql->query("CREATE TABLE Memory(
			MemID INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
			NumberOfModules INT(8),
			MemorySpeed VARCHAR(200),
			MemoryCapacity INT(8),
			ECC VARCHAR(200),
			ReleaseYear INT(8),
			PricePerGigabyte DECIMAL(10,2),
			ManufacturerWarranty VARCHAR(200),
			MemoryCapacityPerModule INT(8),
			CASLatency INT(8),
			TypeOfMemory VARCHAR(200),
			ProductPage VARCHAR(200),
			Voltage VARCHAR(200),
			CompID INT (10) NOT NULL,
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);");

		  $sql->query("CREATE TABLE MotherBoard(
			MoboID INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
			Width VARCHAR(200),
			Cooling VARCHAR(200),
			FormFactor VARCHAR(200),
			PCISlots VARCHAR(200),
			NumberOfHdmiOutputs VARCHAR(200),
			EthernetConnection VARCHAR(200),
			Socket VARCHAR(200),
			HDMI VARCHAR(200),
			HeadPhoneOutput VARCHAR(200),
			TypeOfMemory VARCHAR(200),
			USB2 VARCHAR(200),
			mSATA VARCHAR(200),
			Bluetooth VARCHAR(200),
			ECCSupport VARCHAR(200),
			PCIExpressx8 VARCHAR(200),
			TypeOfRaid VARCHAR(200),
			PCIExpressx1 VARCHAR(200),
			PCIExpressx4 VARCHAR(200),
			ManufacturerWarranty VARCHAR(200),
			PCIExpressVersion VARCHAR(200),
			SoundCard VARCHAR(200),
			PCIExpressx16 VARCHAR(200),
			MemorySpeeds VARCHAR(200),
			ProductPage VARCHAR(200),
			ChassisFanConnectors VARCHAR(200),
			PCIExpressMini VARCHAR(200),
			BluetoothVersion VARCHAR(200),
			MemorySlots INT(5),
			MiniPCI VARCHAR(200),
			DVI VARCHAR(200),
			SupportForMultipleGraphicsCards VARCHAR(200),
			SupportForIIntegratedGraphicsInCPU VARCHAR(200),
			SATAExpress VARCHAR(200),
			MicrophoneInput VARCHAR(200),
			NumberOfEthernetConnections VARCHAR(200),
			PowerFanConnector VARCHAR(200),
			NumberOfDisplayportOutputs VARCHAR(200),
			Chipset VARCHAR(200),
			SATA3Gbs VARCHAR(200),
			MaximumAmountOfMemory VARCHAR(200),
			Displayport VARCHAR(200),
			USB VARCHAR(200),
			SoundCardChip VARCHAR(200),
			RaidController VARCHAR(200),
			Thunderbolt VARCHAR(200),
			64bitProcessor VARCHAR(200),
			U2 VARCHAR(200),
			Depth VARCHAR(200),
			ReleaseYear VARCHAR(200),
			M2 VARCHAR(200),
			VGAOutputs VARCHAR(200),
			WirelessNetwork VARCHAR(200),
			SATA6Gbs VARCHAR(200),
			CompID INT (10) NOT NULL,
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);
		  ");

        echo("Build complete");
        }catch(Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
?>
