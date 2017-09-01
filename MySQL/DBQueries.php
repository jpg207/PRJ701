<?php
    class DBQueries {

        public function Query($query){
            $sql = new DBConnection("compcreator");
            $DBObject = $sql->query("$query");
            $result = $DBObject->fetch_assoc();
            return $result;
        }

        public function DBGetCPU($CPUBudget){
            $rows = array();
            $sql = new DBConnection("compcreator");
            $result = $sql->query("SELECT * FROM component INNER JOIN cpu ON component.CompID = cpu.CompID WHERE component.CompPrice < $CPUBudget ORDER BY cpu.Clockfrequency DESC LIMIT 1");
            while($row = mysqli_fetch_assoc($result)){
                foreach ($row as $col => $value) {
                    $rows[$col]= $value;
                }
            }
            return $rows;
        }

        public function DBGetMOBO(){
            $sql = new DBConnection("compcreator");

        }

        public function DBGetGPU(){
            $sql = new DBConnection("compcreator");

        }

        public function DBGetHDD(){
            $sql = new DBConnection("compcreator");

        }

        public function DBGetSSD(){
            $sql = new DBConnection("compcreator");

        }

        public function DBGetRAM(){
            $sql = new DBConnection("compcreator");

        }

        public function DBGetPSU(){
            $sql = new DBConnection("compcreator");

        }

        public function DBGetCase(){
            $sql = new DBConnection("compcreator");

        }

    }
?>
