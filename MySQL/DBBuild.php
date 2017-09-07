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
			Clockfrequency DECIMAL(10,2),
			Productpage VARCHAR(200),
			L3cache DECIMAL(10,2),
			TurboBoostCore VARCHAR(200),
			CPUtype VARCHAR(200),
			Boxversion VARCHAR(200),
			Numberofthreads INT(8),
			64bitprocessor VARCHAR(200),
			L2cache DECIMAL(10,2),
			Socket VARCHAR(200),
			Numberofcores INT(8),
			Graphicsprocessor VARCHAR(200),
			Releaseyear INT(8),
			ThermalDesignPower INT(8),
			Integratedgraphics VARCHAR(200),
			Virtualization VARCHAR(200),
			CPURating DECIMAL (10, 2) NOT NULL,
			CompID INT (10) NOT NULL,
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE );");

		  $sql->query("CREATE TABLE GPU(
			GPUID INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
			Cooling VARCHAR(200),
			Numberoffans INT(8),
			Semipassive VARCHAR(200),
			Factoryoverclocked VARCHAR(200),
			Graphicsprocessor VARCHAR(200),
			Lowprofile VARCHAR(200),
			Nonreferencecooler VARCHAR(200),
			PCIExpressversion VARCHAR(200),
			DisplayPort VARCHAR(200),
			NumberofDisplayPortoutputs INT(8),
			DVI VARCHAR(200),
			NumberofDVIoutputs INT(8),
			HDMI VARCHAR(200),
			NumberofHDMIoutputs INT(8),
			VGAoutputs VARCHAR(200),
			Maximumresolution VARCHAR(200),
			Numberofsupportedmonitors INT(8),
			Length VARCHAR(200),
			Numberofslots INT(8),
			DirectX DECIMAL(10,2),
			HDR VARCHAR(200),
			OpenGL DECIMAL(10,2),
			Vulkan DECIMAL(10,2),
			Supportformultiplegraphicscards VARCHAR(200),
			Memorybandwidth DECIMAL(10,2),
			Memorycapacity DECIMAL(10,2),
			Memoryinterface DECIMAL(10,2),
			Memoryspeed DECIMAL(10,2),
			Memorytype VARCHAR(200),
			GPUBoost VARCHAR(200),
			Processorspeed DECIMAL(10,2),
			Supplementarypowerconnector VARCHAR(200),
			Manufacturerwarranty INT(8),
			Releaseyear INT(8),
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
			Numberofcardslots INT(8),
			35drivebays INT(8),
			Screwlessdesign VARCHAR(200),
			Format VARCHAR(200),
			Positionofthepowersupply VARCHAR(200),
			Volume VARCHAR(200),
			Supportedmotherboards VARCHAR(200),
			Colour VARCHAR(200),
			Roomforexpansion VARCHAR(200),
			Releaseyear INT(8),
			Maximumlengthofvideocard DECIMAL(10,2),
			Heightofexpansionslots VARCHAR(200),
			Activecooling VARCHAR(200),
			525drivebays INT(8),
			Weight VARCHAR(200),
			Watercooling VARCHAR(200),
			Fanspacestotal VARCHAR(200),
			Maximummotherboardsize VARCHAR(200),
			25drivebays INT(8),
			MaxCPUcoolerheight DECIMAL(10,2),
			Builtinwatercooling VARCHAR(200),
			Frontconnections VARCHAR(200),
			CompID INT (10) NOT NULL,
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);");

		  $sql->query("CREATE TABLE PSU(
			PSUID INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
			Capacity  INT(8),
			Releaseyear INT(8),
			Fansize INT(8),
			Modular VARCHAR(200),
			Cablesocks VARCHAR(200),
			PowerconnectorsforSATA INT(8),
			PowerconnectionsforPCIExpress INT(8),
			Temperaturecontrolledfan VARCHAR(200),
			Manufacturerwarranty INT(8),
			Productpage VARCHAR(200),
			Efficiency INT(8),
			Numberoffans INT(8),
			Semipassive VARCHAR(200),
			Format VARCHAR(200),
			80pluscertification VARCHAR(200),
			CompID INT (10) NOT NULL,
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);");

		  $sql->query("CREATE TABLE SSD(
			SSDID INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
			Maximumreadspeed INT(8),
			Controllerchip VARCHAR(200),
			PriceGB DECIMAL(10,2),
			Manufacturerwarranty INT(8),
			Interface VARCHAR(200),
			Formfactor VARCHAR(200),
			Typeofflashmemory VARCHAR(200),
			Connection VARCHAR(200),
			Productpage VARCHAR(200),
			Weight VARCHAR(200),
			Maximumwritespeed INT(8),
			Size INT(8),
			CompID INT (10) NOT NULL,
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);");

		  $sql->query("CREATE TABLE HDD(
			HDDID INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
			Internaltransferrate INT(8),
			Productpage VARCHAR(200),
			Hybriddisk VARCHAR(200),
			PriceTB DECIMAL(10,2),
			Formfactor VARCHAR(200),
			Manufacturerwarranty INT(8),
			Interface VARCHAR(200),
			Cachesize INT(8),
			Connection VARCHAR(200),
			Releaseyear INT(8),
			Rotationalspeed VARCHAR(200),
			Noiselevel VARCHAR(200),
			Harddrivesize DECIMAL(10,2),
			CompID INT (10) NOT NULL,
			FOREIGN KEY (CompID) REFERENCES Component(CompID) ON DELETE CASCADE);");

		  $sql->query("CREATE TABLE Memory(
			MemID INT(10) AUTO_INCREMENT PRIMARY KEY NOT NULL,
			Numberofmodules INT(8),
			Memoryspeed VARCHAR(200),
			Memorycapacity INT(8),
			ECC VARCHAR(200),
			Releaseyear INT(8),
			PriceGB DECIMAL(10,2),
			Manufacturerwarranty VARCHAR(200),
			Memorycapacitypermodule INT(8),
			CASLatency INT(8),
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
			Memoryslots INT(5),
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
