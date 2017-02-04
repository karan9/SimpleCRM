<?php 
/**
*
* A Simple Class That Encapsulates Database for me
*
*/
require_once 'meekrodb.2.3.class.php';

class SystemHandler {
    
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