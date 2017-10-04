<?php
    include('../DBConnection.php');

    try {
        $sql = new DBConnection("compcreator");
        $sql->query("DROP DATABASE IF EXISTS CompCreator;");
        $sql->query("CREATE DATABASE CompCreator CHARACTER SET utf8 COLLATE utf8_unicode_ci;");
        $sql->query("USE CompCreator;");
        $sql->query("DROP TABLE IF EXISTS ComponentDetail;");
        $sql->query("DROP TABLE IF EXISTS ComponentIdentifyer;");

		$sql->query("CREATE TABLE ComponentIdentifyer(
			CompID INT(10) PRIMARY KEY NOT NULL,
			CompName VARCHAR(100) NOT NULL,
            CompCategory VARCHAR(35) NOT NULL,
			CompPrice DECIMAL(6,2) NOT NULL,
			CompLink VARCHAR(200) NOT NULL,
            CompRating INT(4) DEFAULT 99999,
            CompDate  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP());
        ");

		$sql->query("CREATE TABLE ComponentDetail(
			CompID INT (10) NOT NULL,
            DetailTitle  VARCHAR(200) NOT NULL,
            DetailValue  VARCHAR(200) NOT NULL,
            DetailValueNumeric DECIMAL(10,2),
			FOREIGN KEY (CompID) REFERENCES ComponentIdentifyer(CompID) ON DELETE CASCADE );
        ");

        echo("Build complete");
        }catch(Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
?>
