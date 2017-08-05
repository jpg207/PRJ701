<?php
    //Data base connection object used for connection to the database
    class DBconnection {
        private $rs;
        private $connectRs;
        private $fetchResult;
        private $DBi;

        private function connectDb($pStrDatabase)
        {
            error_reporting( 0 );
            $this->connectRs = mysqli_connect("localhost","compcreator","");
            //Local host transfer if required
            if(!$this->connectRs){
                $this->connectRs = mysqli_connect("localhost","root","");
            }
            error_reporting( E_ALL );
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
            $this->rs = -1;
            $this->rs = mysqli_query($this->connectRs,$pStrSQL);
            if( !$this->rs)
            {
                echo "Error running query [$pStrSQL] ".mysqli_error($this->connectRs)."<br>";
                $this->rs = -1;
            }
            return $this->rs;
        }

        public function lastCount(){
            $result = mysqli_num_rows($this->rs);
            return $result;
        }

        public function DBConnection($pStrDatabase)
        {
            $this->connectDb($pStrDatabase);
        }

        public function next(){
            $aRow = mysqli_fetch_assoc($this->rs);
            return $aRow;
        }

        public function free(){
            mysqli_free_result($this->rs);
        }
    }
?>
