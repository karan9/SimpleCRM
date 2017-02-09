<?php
// include Database
require "classes/TransactionHandler.php";
require "helper/helpers.php";

function getJsonEncodedValue($val, $msg) {
    // variable to hold our response
    $response = array("error" => false);
    
    if ($val != null) {
        $response["error"] = false;
        $response["message"] = "success: " . $msg;
        $response["transactions"] = $val;
        return json_encode($response);
    } else {
        $response["error"] = true;
        $response["message"] = "error: " . $msg;
        return json_encode($response);
    }
}

function searchTransactionByCard($card, TransactionHandler $th) {
    $val = $th->findTransactionByCard($card);
    if ($val) {
        return $val;
    } else {
        return false;
    }
}

function searchTransactionByDate($date, TransactionHandler $th) {
    $val = $th->findTransactionBydate($date);
    if ($val) {
        return $val;
    } else {
        return false;
    }
}

function searchTransactionByUid($uid, TransactionHandler $th) {
    $val = $th->findTransactionById($uid);
    if ($val) {
        return $val;
    } else {
        return false;
    }
} 

function main() {
    $card = $_POST["searchCARD"];
    $uid = $_POST["searchUID"];
    $date = $_POST["searchDATE"];

    $th = new TransactionHandler();
    
    if ($date != true && $uid != true) {
        $val = searchTransactionByCard($card, $th);
        if ($val) {
            header("Content-Type: application/json");
            echo getJsonEncodedValue($val, "Transactions Found");
        } else {
            header("Content-Type: application/json");
            echo getJsonEncodedValue(null, "No, Transactions Found");
        }

        return;
    }

    if ($uid != true && $card != true) {
        $val = searchTransactionByDate($date, $th);
        if ($val) {
            header("Content-Type: application/json");
            echo getJsonEncodedValue($val, "Transactions Found");
        } else {
            header("Content-Type: application/json");
            echo getJsonEncodedValue(null, "No, Transactions Found");
        }
        return;
    }

    if ($date != true && $card != true ) {
        $val = searchTransactionByUid($uid, $th); 
        if ($val) {
            header("Content-Type: application/json");
            echo getJsonEncodedValue($val, "Transactions Found");
        } else {
            header("Content-Type: application/json");
            echo getJsonEncodedValue(null, "No, Transactions Found");
        }
        return;
    }
}

main();

?>