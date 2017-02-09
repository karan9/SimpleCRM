<?php
class DB_Connect {
    private $conn;
 
    // Connecting to database
    public function connect() {
        // Connecting to mysql database
        $this->conn = new mysqli('localhost','mohit','Newyork@27','mohit_crm_db');
        // return database handler
        return $this->conn;
    }
}
?>