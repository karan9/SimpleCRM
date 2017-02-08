<?php
class DB_Connect {
    private $conn;
 
    // Connecting to database
    public function connect() {       
        // Connecting to mysql database
        $this->conn = new mysqli('localhost','dfdfdsf','ddasdsad','dsfdfdsf');

        // return database handler
        return $this->conn;
    }
}
?>