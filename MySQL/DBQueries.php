<?php
    class DBQueries {

        public function Query($query){
            $sql = new DBConnection("compcreator");
            $DBObject = $sql->query("$query");
            $result = $DBObject->fetch_assoc();
            return $result;
        }

        public function DBRadioFill(){
            $sql = new DBConnection("compcreator");

        }

    }
?>
