<?php 
/**
*
* A Simple Class That Encapsulates Database for me
*
*/
require_once "../vendor/meekrodb.2.3.class.php";

class UserHandler {
    
    function __construct(DatabaseHandler $db) {
        $this->$db = $db;
    }

    
    /**   Finding User     **/
    
    function findUserById() {

    }

    function findUserByName() {

    }

    /**  Creating User   **/

    function createUser() {

    }

    /**   Updating User   **/

    function updateUserById() {

    }

    /** Removing User **/

    function removeUserById() {

    }
}


?>