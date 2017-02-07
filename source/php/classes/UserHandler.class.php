<?php 
/**
*
* A Simple Class That Encapsulates Database for me
*
*/
require "DbHandler.class.php";
require "DbInfo.class.php";

class UserHandler {
    
    private $dbhandler;

    public function __construct() {
        $dbinfo = new DbInfo();
        $this->$dbhandler = new DbHandler(
            $dbinfo->get_db_user(),
            $dbinfo->get_db_passwd(),
            $dbinfo->get_db_name() 
        );

        // connect the database
        $this->$dbhandler->connect();
    }

    public function createUser($username, $password, $role) {

    }

    public function updateUser($username) {

    } 

    public function removeUser($username) {

    } 

    public function findUserByUsername($username) {

    }

    public function findUserByRole($role) {

    }
}
?>