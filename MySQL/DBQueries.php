<?php
    class DBQueries {

        public function Query($query){
            $sql = new DBConnection("jona331db");
            $DBObject = $sql->query("$query");
            $result = $DBObject->fetch_assoc();
            return $result;
        }


    }
?>
