<?php
    //Data base connection object used for connection to the database
    class DBconnection {
        private $rs;
        private $connectRs;
        private $fetchResult;
        private $DBi;

        private function connectDb($pStrDatabase)
        {
            $this->connectRs = mysqli_connect("localhost","root","");
            if(!$this->connectRs)
            {
                echo "Error connecting to the database server".mysqli_error($this->connectRs);
                $this->connectRs = -1;
            }
            $dbRs = mysqli_select_db($this->connectRs,$pStrDatabase);
            if(! $dbRs)
            {
                echo "Error selecting the database".mysql_error($this->connectRs);
            }
        }

        public function query($pStrSQL)
        {
            $this->rs = mysqli_query($this->connectRs,$pStrSQL);
            if( !$this->rs)
            {
                echo "Error running query [$pStrSQL] ".mysqli_error($this->connectRs)."<br>";
            }
            return $this->rs;
        }

        public function DBConnection($pStrDatabase)
        {
            $this->connectDb($pStrDatabase);
        }
    }
?>
