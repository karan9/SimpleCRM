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

function main() {
    $transaction = new TransactionHandler();

    $val = $transaction->findTransactions();
    if ($val) {
        header("Content-Type: application/json");
        echo getJsonEncodedValue($val, "Transactions Found");
    } else {
        header("Content-Type: application/json");
        echo getJsonEncodedValue(null, "Transactions Not Found");
    }
}

// initiate things!!
main();

?>