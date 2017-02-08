<?php
class DB_Connect {
    private $conn;
 
    // Connecting to database
    public function connect() {
        // Connecting to mysql database
        $this->conn = new mysqli('localhost','ssdsa','dsdsads','dssdsad');
        // return database handler
        return $this->conn;
    }
}
?>