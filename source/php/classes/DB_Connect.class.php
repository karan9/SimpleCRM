<?php
class DB_Connect {
    private $conn;
 
    // Connecting to database
    public function connect() {       
        // Connecting to mysql database
        $this->conn = new mysqli('localhost','dsdsad','fnjkndjska','dnakldsamlkm');

        // return database handler
        return $this->conn;
    }
}
?>