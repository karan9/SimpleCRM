<?php
class DB_Connect {
    private $conn;
 
    // Connecting to database
    public function connect() {       
        // Connecting to mysql database
        $this->conn = new mysqli('localhost','ds','ddsadsa','dsdsads');

        // return database handler
        return $this->conn;
    }
}
?>