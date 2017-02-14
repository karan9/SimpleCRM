<?php 
function init() {
    
}

function getUniqueId() {
    $clientPrefix = 'API';
    $randHash = mt_rand(5000, 10000);

    return $clientPrefix . $randHash;
}

function createUser($username, $password, $role, MySQLi $conn) {
    // generate unique id
    $uid = getUniqueId();
    
    // send user
    $stmt = $conn->prepare("INSERT INTO users(uid, username, password, role, created_at) VALUES(?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $uid, $username, $password, $role);
    $result = $stmt->execute();
    $stmt->close();
    if ($result) {
        return $result;
    } else {
        return false;
    }        
}

function getJsonEncodedValue($val, $msg) {
    // variable to hold our response
    $response = array("error" => false);
    
    if ($val) { 
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
    require_once 'classes/DB_Connect.class.php';
    // connecting to database
    $db = new Db_Connect();
    $conn = $db->connect();

    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($username != true) {
        header("Content-Type: application/json");
        echo getJsonEncodedValue(false, "Error in username");
    } elseif ($password != true) {
        header("Content-Type: application/json");
        echo getJsonEncodedValue(false, "Error in Password");
    } elseif ($role != true) {
        header("Content-Type: application/json");
        echo getJsonEncodedValue(false, "Error in Role");
    } else {
        $role = strtolower($role);

        $val = createUser($username, $password, $role, $conn);
        if ($val) {
            header("Content-Type: application/json");
            echo getJsonEncodedValue($val, "User Successfully Created");
        } else {
            header("Content-Type: application/json");
            echo getJsonEncodedValue(false, "Incorrect Signup details");
        }
    }
}


main();


?>