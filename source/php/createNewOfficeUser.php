<?php 

// include Database
require "classes/UserHandler.class.php";
require "helper/helpers.php";


// userHandler instance

function checkSignupVars($username, $passwd, $role) {
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

function checkSignup(UserHandler $userHandler, $username, $passwd, $role) {
    
    // grab the user
    $user = $userHandler->findUserByUsername($username);
    // check starts
    if ($user) {
        return false;
    } else {
       $ret = $userHandler->createUser($username, $passwd, $role, true);
    }

    // checksFlag
    if ($ret) {
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
    $ret = checkSignupVars($username, $password, $role);
    if ($ret) {
        $val = checkSignup($userHandler, $username, $password, $role);
        if ($val) {
            header("Content-Type: application/json");
            echo getJsonEncodedValue($val, "User Successfully Created");
        } else {
            header("Content-Type: application/json");
            echo getJsonEncodedValue(null, "Incorrect Signup details");
        }
    } else {
        header("Content-Type: application/json");
        echo getJsonEncodedValue(null, "Please Check Your SignUp Details");  
    }
}

// initiate things!!
main();

?>