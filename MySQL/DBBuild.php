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

          $sql->query("CREATE TABLE Component( 	CompID INT(10) PRIMARY KEY NOT NULL, 	CompName VARCHAR(50) NOT NULL,     CompPrice DECIMAL(6,2) NOT NULL,     CompLink VARCHAR(50) NOT NULL );");

          $sql->query("CREATE TABLE CPU( 	CPUID INT(10) PRIMARY KEY NOT NULL,     CPUClock DECIMAL (10,2) NOT NULL,     CPUCores INT (2) NOT NULL,     CPUThreads INT (2) NOT NULL,     CPUIGPU BOOL NOT NULL,     CPURating DECIMAL (10, 2) NOT NULL,     CompID INT (10) NOT NULL,     FOREIGN KEY (CompID) REFERENCES Component(CompID) );");

          $sql->query("CREATE TABLE GPU( 	GPUID INT(10) PRIMARY KEY NOT NULL,     GPUClock INT (10) NOT NULL,     GPUMemoryCapacity INT(10) NOT NULL,     GPUMemoryType VARCHAR(10) NOT NULL,     GPURating DECIMAL (10,2) NOT NULL,     CompID INT (10) NOT NULL,     FOREIGN KEY (CompID) REFERENCES Component(CompID) );");

          $sql->query("CREATE TABLE SystemCase( 	CaseID INT(10) PRIMARY KEY NOT NULL,     CaseSize VARCHAR(50) NOT NULL,     MaxMoboSize VARCHAR(10) NOT NULL,     CompID INT (10) NOT NULL,     FOREIGN KEY (CompID) REFERENCES Component(CompID) );");

          $sql->query("CREATE TABLE PSU( 	PSUID INT(10) PRIMARY KEY NOT NULL,     PSU80Rating INT(10) NOT NULL,     PSUModular TINYINT(1) NOT NULL,     PSUWattage INT(10) NOT NULL,     CompID INT (10) NOT NULL,     FOREIGN KEY (CompID) REFERENCES Component(CompID) );");

          $sql->query("CREATE TABLE StorageDrive( 	SDID INT(10) PRIMARY KEY NOT NULL,     SDType VARCHAR(10) NOT NULL,     SDCapacity INT(10) NOT NULL,     SDSpeed INT(5) NOT NULL,     CompID INT (10) NOT NULL,     FOREIGN KEY (CompID) REFERENCES Component(CompID) );");

          $sql->query("CREATE TABLE Memory( 	MemID INT(10) PRIMARY KEY NOT NULL,     MemCapacity INT(10) NOT NULL,     MemType VARCHAR(10) NOT NULL,     MemSpeed INT(10) NOT NULL,     CompID INT (10) NOT NULL,     FOREIGN KEY (CompID) REFERENCES Component(CompID) );");

          $sql->query("CREATE TABLE MotherBoard( 	MoboID INT(10) PRIMARY KEY NOT NULL,     MoboSocketType INT(10) NOT NULL,     MoboRamSticks Int(10) NOT NULL,     CompID INT (10) NOT NULL,     FOREIGN KEY (CompID) REFERENCES Component(CompID) );
          ");

        echo("Build complete");
        }catch(Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
?>
