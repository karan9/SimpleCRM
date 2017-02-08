<?php 

// include Database
require "classes/UserHandler.class.php";
require "helper/helpers.php";


// userHandler instance

function checkAccessVars($username, $role) {
    // check if they are empty
    if ($username != true || $role != true) {
        return false;
    }

    // check if they are proper strings
    if (!is_string($username) || !is_string($role)) {
        return false;
    }

    // check if any html tags are present
    if (is_html($username) || is_html($role)) {
        return false;
    }

    // if everything checks out
    return true;
}

function checkAccess(UserHandler $userHandler, $username, $role) {
    
    // grab the user
    $user = $userHandler->findUserByUsername($username);
    $checkFlag = 0;
    
    // check starts
    if ($user) {
        if($username != $user['username']) {
            $checkFlag += 1;
        } elseif ($role != $user['role']) {
            $checkFlag += 1;
        }
    } else {
        return FALSE;
    }

    // checksFlag
    if ($checkFlag == 0) {
        return $user;
    } else {
        return FALSE;
    }
}

function getJsonEncodedValue($val, $msg) {
    // variable to hold our response
    $response = array("error" => false);
    
    if ($val != null) { 
        $response["error"] = false;
        $response["message"] = "success: " . $msg;
        $response["user"] = $val;
        return json_encode($response);
    } else {
        $response["error"] = true;
        $response["message"] = "error: " . $msg;
        return json_encode($response);
    }
}

function main() {
    $username = $_POST['username'];
    $role = $_POST['role'];
    $userHandler = new UserHandler();
    // checkAccessVars
    $ret = checkAccessVars($username, $role);
    if ($ret) {
        $val = checkAccess($userHandler, $username, $role);
        if ($val) {
            header("Content-Type: application/json");
            echo getJsonEncodedValue($val, "Admin Validation Successfull");
        } else {
            header("Content-Type: application/json");
            echo getJsonEncodedValue(null, "Admin Validation is Not Successfull");
        }
    } else {
        header("Content-Type: application/json");
        echo getJsonEncodedValue(null, "Error, Incorrect Validation For Admin");  
    }
}

// initiate things!!
main();

?>