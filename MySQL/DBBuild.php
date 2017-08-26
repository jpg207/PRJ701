<?php
    include('DBConnection.php');

    try {
          $sql = new DBConnection("compcreator");
		  $sql->query("DROP DATABASE IF EXISTS CompCreator;");
		  $sql->query("CREATE DATABASE CompCreator;");
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
			CompPrice VARCHAR(200) NOT NULL,
			CompLink VARCHAR(200) NOT NULL );");

		  $sql->query("CREATE TABLE CPU(
			CPUID INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
			Clockfrequency VARCHAR(200),
			Productpage VARCHAR(200),
			L3cache VARCHAR(200),
			TurboBoostCore VARCHAR(200),
			CPUtype VARCHAR(200),
			Boxversion VARCHAR(200),
			Numberofthreads VARCHAR(200),
			64bitprocessor VARCHAR(200),
			L2cache VARCHAR(200),
			Socket VARCHAR(200),
			Numberofcores VARCHAR(200),
			Graphicsprocessor VARCHAR(200),
			Releaseyear VARCHAR(200),
			ThermalDesignPower VARCHAR(200),
			Integratedgraphics VARCHAR(200),
			Virtualization VARCHAR(200),
			CPURating DECIMAL (10, 2) NOT NULL,
			CompID INT (10) NOT NULL,
			FOREIGN KEY (CompID) REFERENCES Component(CompID) );");

		  $sql->query("CREATE TABLE GPU(
			GPUID INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
			Cooling VARCHAR(200),
			Numberoffans VARCHAR(200),
			Semipassive VARCHAR(200),
			Factoryoverclocked VARCHAR(200),
			Graphicsprocessor VARCHAR(200),
			Lowprofile VARCHAR(200),
			Nonreferencecooler VARCHAR(200),
			PCIExpressversion VARCHAR(200),
			DisplayPort VARCHAR(200),
			NumberofDisplayPortoutputs VARCHAR(200),
			DVI VARCHAR(200),
			NumberofDVIoutputs VARCHAR(200),
			HDMI VARCHAR(200),
			NumberofHDMIoutputs VARCHAR(200),
			VGAoutputs VARCHAR(200),
			Maximumresolution VARCHAR(200),
			Numberofsupportedmonitors VARCHAR(200),
			Length VARCHAR(200),
			Numberofslots VARCHAR(200),
			DirectX VARCHAR(200),
			HDR VARCHAR(200),
			OpenGL VARCHAR(200),
			Vulkan VARCHAR(200),
			Supportformultiplegraphicscards VARCHAR(200),
			Memorybandwidth VARCHAR(200),
			Memorycapacity VARCHAR(200),
			Memoryinterface VARCHAR(200),
			Memoryspeed VARCHAR(200),
			Memorytype VARCHAR(200),
			GPUBoost VARCHAR(200),
			Processorspeed VARCHAR(200),
			Supplementarypowerconnector VARCHAR(200),
			Manufacturerwarranty VARCHAR(200),
			Releaseyear VARCHAR(200),
			Productpage VARCHAR(200),
			GPURating DECIMAL (10,2),
			CompID INT (10) NOT NULL,
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);");

		  $sql->query("CREATE TABLE SystemCase(
			CaseID INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
			Typeofchassis VARCHAR(200),
			Material VARCHAR(200),
			Dimensions VARCHAR(200),
			Productpage VARCHAR(200),
			Numberofcardslots VARCHAR(200),
			35drivebays VARCHAR(200),
			Screwlessdesign VARCHAR(200),
			Format VARCHAR(200),
			Positionofthepowersupply VARCHAR(200),
			Volume VARCHAR(200),
			Supportedmotherboards VARCHAR(200),
			Colour VARCHAR(200),
			Roomforexpansion VARCHAR(200),
			Releaseyear VARCHAR(200),
			Maximumlengthofvideocard VARCHAR(200),
			Heightofexpansionslots VARCHAR(200),
			Activecooling VARCHAR(200),
			525drivebays VARCHAR(200),
			Weight VARCHAR(200),
			Watercooling VARCHAR(200),
			Fanspacestotal VARCHAR(200),
			Maximummotherboardsize VARCHAR(200),
			25drivebays VARCHAR(200),
			MaxCPUcoolerheight VARCHAR(200),
			Builtinwatercooling VARCHAR(200),
			Frontconnections VARCHAR(200),
			CompID INT (10) NOT NULL,
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);");

		  $sql->query("CREATE TABLE PSU(
			PSUID INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
			Capacity  VARCHAR(200),
			Releaseyear VARCHAR(200),
			Fansize VARCHAR(200),
			Modular VARCHAR(200),
			Cablesocks VARCHAR(200),
			PowerconnectorsforSATA VARCHAR(200),
			PowerconnectionsforPCIExpress VARCHAR(200),
			Temperaturecontrolledfan VARCHAR(200),
			Manufacturerwarranty VARCHAR(200),
			Productpage VARCHAR(200),
			Efficiency VARCHAR(200),
			Numberoffans VARCHAR(200),
			Semipassive VARCHAR(200),
			Format VARCHAR(200),
			80pluscertification VARCHAR(200),
			CompID INT (10) NOT NULL,
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);");

		  $sql->query("CREATE TABLE SSD(
			SSDID INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
			Maximumreadspeed VARCHAR(200),
			Controllerchip VARCHAR(200),
			PriceGB VARCHAR(200),
			Manufacturerwarranty VARCHAR(200),
			Interface VARCHAR(200),
			Formfactor VARCHAR(200),
			Typeofflashmemory VARCHAR(200),
			Connection VARCHAR(200),
			Productpage VARCHAR(200),
			Weight VARCHAR(200),
			Maximumwritespeed VARCHAR(200),
			Size VARCHAR(200),
			CompID INT (10) NOT NULL,
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);");

		  $sql->query("CREATE TABLE HDD(
			HDDID INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
			Internaltransferrate VARCHAR(200),
			Productpage VARCHAR(200),
			Hybriddisk VARCHAR(200),
			PriceTB VARCHAR(200),
			Formfactor VARCHAR(200),
			Manufacturerwarranty VARCHAR(200),
			Interface VARCHAR(200),
			Cachesize VARCHAR(200),
			Connection VARCHAR(200),
			Releaseyear VARCHAR(200),
			Rotationalspeed VARCHAR(200),
			Noiselevel VARCHAR(200),
			Harddrivesize VARCHAR(200),
			CompID INT (10) NOT NULL,
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);");

		  $sql->query("CREATE TABLE Memory(
			MemID INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
			Numberofmodules VARCHAR(200),
			Memoryspeed VARCHAR(200),
			Memorycapacity VARCHAR(200),
			ECC VARCHAR(200),
			Releaseyear VARCHAR(200),
			PriceGB VARCHAR(200),
			Manufacturerwarranty VARCHAR(200),
			Memorycapacitypermodule VARCHAR(200),
			CASLatency VARCHAR(200),
			Typeofmemory VARCHAR(200),
			Productpage VARCHAR(200),
			Voltage VARCHAR(200),
			CompID INT (10) NOT NULL,
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);");

		  $sql->query("CREATE TABLE MotherBoard(
			MoboID INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
			Width VARCHAR(200),
			Cooling VARCHAR(200),
			Formfactor VARCHAR(200),
			PCIslots VARCHAR(200),
			NumberofHDMIoutputs VARCHAR(200),
			Ethernetconnection VARCHAR(200),
			Socket VARCHAR(200),
			HDMI VARCHAR(200),
			Headphoneoutput VARCHAR(200),
			Typeofmemory VARCHAR(200),
			USB20 VARCHAR(200),
			mSATA VARCHAR(200),
			Bluetooth VARCHAR(200),
			ECCsupport VARCHAR(200),
			PCIExpressx8 VARCHAR(200),
			TypeofRAID VARCHAR(200),
			PCIExpressx1 VARCHAR(200),
			PCIExpressx4 VARCHAR(200),
			Manufacturerwarranty VARCHAR(200),
			PCIExpressversion VARCHAR(200),
			Soundcard VARCHAR(200),
			PCIExpressx16 VARCHAR(200),
			Memoryspeeds VARCHAR(200),
			Productpage VARCHAR(200),
			Chassisfanconnectors VARCHAR(200),
			PCIExpressMini VARCHAR(200),
			Bluetoothversion VARCHAR(200),
			Memoryslots VARCHAR(200),
			MiniPCI VARCHAR(200),
			DVI VARCHAR(200),
			Supportformultiplegraphicscards VARCHAR(200),
			SupportforintegratedgraphicsinCPU VARCHAR(200),
			SATAExpress VARCHAR(200),
			Microphoneinput VARCHAR(200),
			NumberofEthernetconnections VARCHAR(200),
			Powerfanconnector VARCHAR(200),
			NumberofDisplayPortoutputs VARCHAR(200),
			Chipset VARCHAR(200),
			SATA3Gbs VARCHAR(200),
			Maximumamountofmemory VARCHAR(200),
			DisplayPort VARCHAR(200),
			USB VARCHAR(200),
			Soundcardchip VARCHAR(200),
			RAIDcontroller VARCHAR(200),
			Thunderbolt VARCHAR(200),
			64bitprocessor VARCHAR(200),
			U2 VARCHAR(200),
			Depth VARCHAR(200),
			Releaseyear VARCHAR(200),
			M2 VARCHAR(200),
			VGAoutputs VARCHAR(200),
			Wirelessnetwork VARCHAR(200),
			SATA6Gbs VARCHAR(200),
			CompID INT (10) NOT NULL,
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);
		  ");

        echo("Build complete");
        }catch(Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
?>
