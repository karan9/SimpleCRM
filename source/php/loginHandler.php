<?php 

// include Database
require_once "classes/UserHandler.class.php";
require_once "constants/definations.php";
require_once "helper/helpers.php";

function checkLoginVars($username, $passwd, $role) {
    // check if they are empty
    if ($username != true || $passwd != true || $role != true) {
        return false;
    }

    // check if they are proper strings
    if (!is_string($username) || !is_string($passwd) || !is_string($role)) {
        return false;
    }

    // check if any html tags are present
    if (is_html($username) || is_html($passwd) || is_html($role)) {
        return false;
    }

    // if everything checks out
    return true;
}

function checkLogin($username, $passwd, $role) {
    return array("message" => "this is working");
}


function main() {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // checkLoginVars
    $ret = checkLoginVars($username, $password, $role);
    if ($ret) {
        $val = checkLogin($username, $password, $role);
        if ($val) {
            echo json_encode($val);
        } else {
            echo json_encode($json_error);
        }
    } else {
        echo json_encode($json_error);  
    }
}

// initiate things!!
main();

?>