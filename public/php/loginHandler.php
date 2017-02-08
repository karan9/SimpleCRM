<?php 

// include Database
require "classes/UserHandler.class.php";
require "helper/helpers.php";


// userHandler instance

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

function checkLogin(UserHandler $userHandler, $username, $passwd, $role) {
    
    // grab the user
    $user = $userHandler->findUserByUsername($username);
    $checkFlag = 0;
    
    // check starts
    if ($user) {
        if($username != $user['username']) {
            $checkFlag += 1;
        } elseif ($passwd != $user['password']) {
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
    $password = $_POST['password'];
    $role = $_POST['role'];
    $userHandler = new UserHandler();
    // checkLoginVars
    $ret = checkLoginVars($username, $password, $role);
    if ($ret) {
        $val = checkLogin($userHandler, $username, $password, $role);
        if ($val) {
            echo getJsonEncodedValue($val, "Successfull Login");
        } else {
            echo getJsonEncodedValue(null, "Incorrect Login details");
        }
    } else {
        echo getJsonEncodedValue(null, "Please Check Your Login Details");  
    }
}

// initiate things!!
main();

?>