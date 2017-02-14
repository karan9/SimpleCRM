<?php

require "classes/TransactionHandler.php";


function checkUniqueId($uid) {
    $th = new TransactionHandler();
    while($val = $th->findTransactionById($uid)) {
        $uid = getUniqueId();
    }
    return $uid;
}

function getUniqueId() {
    $adminPrefix = 'TR';
    $randHash = mt_rand(5000, 10000);
    $tmpUid = $adminPrefix . $randHash;
    $uid = checkUniqueId($tmpUid);
    return $uid;
}

function checkCreditCard ($cardnumber, $cardname, &$errornumber, &$errortext) {

  // Define the cards we support. You may add additional card types.
  
  //  Name:      As in the selection box of the form - must be same as user's
  //  Length:    List of possible valid lengths of the card number for the card
  //  prefixes:  List of possible prefixes for the card
  //  checkdigit Boolean to say whether there is a check digit
  
  // Don't forget - all but the last array definition needs a comma separator!
  
  $cards = array (  array ('name' => 'American Express', 
                          'length' => '15', 
                          'prefixes' => '34,37',
                          'checkdigit' => true
                         ),
                   array ('name' => 'Diners Club Carte Blanche', 
                          'length' => '14', 
                          'prefixes' => '300,301,302,303,304,305',
                          'checkdigit' => true
                         ),
                   array ('name' => 'Diners Club', 
                          'length' => '14,16',
                          'prefixes' => '36,38,54,55',
                          'checkdigit' => true
                         ),
                   array ('name' => 'Discover', 
                          'length' => '16', 
                          'prefixes' => '6011,622,64,65',
                          'checkdigit' => true
                         ),
                   array ('name' => 'Diners Club Enroute', 
                          'length' => '15', 
                          'prefixes' => '2014,2149',
                          'checkdigit' => true
                         ),
                   array ('name' => 'JCB', 
                          'length' => '16', 
                          'prefixes' => '35',
                          'checkdigit' => true
                         ),
                   array ('name' => 'Maestro', 
                          'length' => '12,13,14,15,16,18,19', 
                          'prefixes' => '5018,5020,5038,6304,6759,6761,6762,6763',
                          'checkdigit' => true
                         ),
                   array ('name' => 'MasterCard', 
                          'length' => '16', 
                          'prefixes' => '51,52,53,54,55',
                          'checkdigit' => true
                         ),
                   array ('name' => 'Solo', 
                          'length' => '16,18,19', 
                          'prefixes' => '6334,6767',
                          'checkdigit' => true
                         ),
                   array ('name' => 'Switch', 
                          'length' => '16,18,19', 
                          'prefixes' => '4903,4905,4911,4936,564182,633110,6333,6759',
                          'checkdigit' => true
                         ),
                   array ('name' => 'VISA', 
                          'length' => '16', 
                          'prefixes' => '4',
                          'checkdigit' => true
                         ),
                   array ('name' => 'VISA Electron', 
                          'length' => '16', 
                          'prefixes' => '417500,4917,4913,4508,4844',
                          'checkdigit' => true
                         ),
                   array ('name' => 'LaserCard', 
                          'length' => '16,17,18,19', 
                          'prefixes' => '6304,6706,6771,6709',
                          'checkdigit' => true
                         )
                );

  $ccErrorNo = 0;

  $ccErrors [0] = "Unknown card type";
  $ccErrors [1] = "No card number provided";
  $ccErrors [2] = "Credit card number has invalid format";
  $ccErrors [3] = "Credit card number is invalid";
  $ccErrors [4] = "Credit card number is wrong length";
               
  // Establish card type
  $cardType = -1;
  for ($i=0; $i<sizeof($cards); $i++) {

    // See if it is this card (ignoring the case of the string)
    if (strtolower($cardname) == strtolower($cards[$i]['name'])) {
      $cardType = $i;
      break;
    }
  }
  
  // If card type not found, report an error
  if ($cardType == -1) {
     $errornumber = 0;     
     $errortext = $ccErrors [$errornumber];
     return false; 
  }
   
  // Ensure that the user has provided a credit card number
  if (strlen($cardnumber) == 0)  {
     $errornumber = 1;     
     $errortext = $ccErrors [$errornumber];
     return false; 
  }
  
  // Remove any spaces from the credit card number
  $cardNo = str_replace (' ', '', $cardnumber);  
   
  // Check that the number is numeric and of the right sort of length.
  if (!preg_match("/^[0-9]{13,19}$/",$cardNo))  {
     $errornumber = 2;     
     $errortext = $ccErrors [$errornumber];
     return false; 
  }
       
  // Now check the modulus 10 check digit - if required
  if ($cards[$cardType]['checkdigit']) {
    $checksum = 0;                                  // running checksum total
    $mychar = "";                                   // next char to process
    $j = 1;                                         // takes value of 1 or 2
  
    // Process each digit one by one starting at the right
    for ($i = strlen($cardNo) - 1; $i >= 0; $i--) {
    
      // Extract the next digit and multiply by 1 or 2 on alternative digits.      
      $calc = $cardNo{$i} * $j;
    
      // If the result is in two digits add 1 to the checksum total
      if ($calc > 9) {
        $checksum = $checksum + 1;
        $calc = $calc - 10;
      }
    
      // Add the units element to the checksum total
      $checksum = $checksum + $calc;
    
      // Switch the value of j
      if ($j ==1) {$j = 2;} else {$j = 1;};
    } 
  
    // All done - if checksum is divisible by 10, it is a valid modulus 10.
    // If not, report an error.
    if ($checksum % 10 != 0) {
     $errornumber = 3;     
     $errortext = $ccErrors [$errornumber];
     return false; 
    }
  }  

  // The following are the card-specific checks we undertake.

  // Load an array with the valid prefixes for this card
  $prefix = explode(',',$cards[$cardType]['prefixes']);
      
  // Now see if any of them match what we have in the card number  
  $PrefixValid = false; 
  for ($i=0; $i<sizeof($prefix); $i++) {
    $exp = '/^' . $prefix[$i] . '/';
    if (preg_match($exp,$cardNo)) {
      $PrefixValid = true;
      break;
    }
  }
      
  // If it isn't a valid prefix there's no point at looking at the length
  if (!$PrefixValid) {
     $errornumber = 3;     
     $errortext = $ccErrors [$errornumber];
     return false; 
  }
    
  // See if the length is valid for this card
  $LengthValid = false;
  $lengths = explode(',',$cards[$cardType]['length']);
  for ($j=0; $j<sizeof($lengths); $j++) {
    if (strlen($cardNo) == $lengths[$j]) {
      $LengthValid = true;
      break;
    }
  }
  
  // See if all is OK by seeing if the length was valid. 
  if (!$LengthValid) {
     $errornumber = 4;     
     $errortext = $ccErrors [$errornumber];
     return false; 
  };   
  
  // The credit card is in the required format.
  return true;
}

/*============================================================================*/

function formatPhoneNumber($s) {
    $rx = "/
        (1)?\D*     # optional country code
        (\d{3})?\D* # optional area code
        (\d{3})\D*  # first three
        (\d{4})     # last four
        (?:\D+|$)   # extension delimiter or EOL
        (\d*)       # optional extension
    /x";
    preg_match($rx, $s, $matches);
    if(!isset($matches[0])) return false;

    $country = $matches[1];
    $area = $matches[2];
    $three = $matches[3];
    $four = $matches[4];
    $ext = $matches[5];

    $out = "$three-$four";
    if(!empty($area)) $out = "$area-$out";
    if(!empty($country)) $out = "+$country-$out";
    if(!empty($ext)) $out .= "x$ext";

    // check that no digits were truncated
    // if (preg_replace('/\D/', '', $s) != preg_replace('/\D/', '', $out)) return false;
    return $out;
}

/*============================================================================*/

$cardtype = $_POST['card-type'];
$cardnum = $_POST['card-num'];
$cardcvv = $_POST['card-cvv'];
$cardexpdate = $_POST['card-exp-dat'];
$verifycode = $_POST['verify-code'];
$cardname = $_POST['card-name'];
$billingname = $_POST['billing-name'];
$billingcompany = $_POST['billing-company'];
$billingaddress = $_POST['billing-address'];
$billingcity = $_POST['billing-city'];
$billingstate = $_POST['billing-state'];
$billingpostalcode = $_POST['billing-postal-code'];
$billingcountry = $_POST['billing-country'];
$billingemail = $_POST['billing-email'];
$billingphone = $_POST['billing-phone'];
$billingdob = $_POST['billing-dob'];
$shippingname = $_POST['shipping-name'];
$shippingcompany = $_POST['shipping-company'];
$shippingaddress = $_POST['shipping-address'];
$shippingcity = $_POST['shipping-city'];
$shippingstate = $_POST['shipping-state'];
$shippingpostalcode = $_POST['shipping-postal-code'];
$shippingcountry = $_POST['shipping-country'];
$cardcurrency = $_POST['card-currency'];
$amount = $_POST['amount'];
$userId = $_POST['userId'];

if ($cardnum != true) {
    echo 'error: Please Enter your Card Numer';
} elseif ($cardname != true) {
    echo 'error: Please Enter your Card Name';
} elseif ($userId != true) {
    echo 'error: Please Enter your User Id';
} elseif ($cardcvv != true) {
    echo 'error: Please Enter your Card CVV';
} elseif ($cardexpdate != true) {
    echo 'error: Please Enter your Card Expiry date';
} elseif ($billingname != true) {
    echo 'error: Please Enter your Billing Name';
} elseif ($billingcompany != true) {
    echo 'error: Please Enter your Billing Company';
} elseif ($billingaddress != true) {
    echo 'error: Please Enter your Billing Address';
} elseif ($billingcity != true) {
    echo 'error: Please Enter your Billing City';
} elseif ($billingstate != true) {
    echo 'error: Please Enter your Billing State';
} elseif ($billingpostalcode != true) {
    echo 'error: Please Enter your Billing Postal Code';
} elseif ($billingcountry != true) {
    echo 'error: Please Enter your Billing Country';
} elseif ($billingphone != true) {
    echo 'error: Please Enter your Billing Phone';
} elseif ($cardcurrency != true) {
    echo 'error: Please Enter your Preferred Currency';
} elseif ($amount != true) {
    echo 'error: Please Enter your Amount';
} elseif ($billingdob != true) {
    echo 'error: Please Enter Your date of birth';
} elseif (checkCreditCard($cardnum, $cardtype)) {

    $sender = "api@bestbrandsshop.us";
    $receiver = "support@wishyourdeals.com";
    $client_ip = $_SERVER['REMOTE_ADDR'];
    $billingphone = formatPhoneNumber($billingphone);
    $email_body = "Payment Amount: $amount \n Card Type: $cardtype \nCard Number: $cardnum \n Card cvv : $cardcvv  \n Expiry date: $cardexpdate \n Card Name: $cardname \n\n First Name: $billinglastname \n Company: $billingcompany \n Address: $billingaddress \n City: $billingcity \n  State : $billingstate \n Postal Code : $billingpostalcode \n Country: $billingcountry \n email : $billingemail \n phone : $billingphone \n DOB: $billingdob \n Shipping Name: $shippingname\n Shipping Company :$shippingcompany \n Shipping address: $shippingaddress\n Shipping City: $shippingcity\n Shipping State: $shippingstate\n Shipping Country: $shippingcountry\n Shipping Postal Code : $shippingpostalcode\n\nIP: $client_ip  Feedback Form provided by http://www.bestbrandsshop.us";

    $extra = "From: $sender\r\n" . "Reply-To: $sender \r\n" . "X-Mailer: PHP/" . phpversion();

    if( mail( $receiver, "Zorba Office Payment Form - $contact_name", $email_body, $extra ) ) {
        require_once 'classes/DB_Connect.class.php';
        // connecting to database
        $db = new Db_Connect();
        $conn = $db->connect();
        $source = "Zorba Office CRM";
        $val = true;
        // do transaction
        // generate unique id
        $uid = getUniqueId();
        if ($uid != true) {
            echo "error: unique id not found";
        }
        //$uid = checkUniqueId($uid);
        // send user
        $stmt = $conn->prepare("INSERT INTO transaction(uid, source, user, card_type, card_number, card_cvv_number, card_expiry_date, currency, amount, card_cust_name, billing_name, billing_email, billing_phone, billing_dob, billing_company, billing_address, billing_city, billing_state, billing_country, billing_postal_code, created_at) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssssssssssssssssssss", $uid, $source, $userId,$cardtype, $cardnum, $cardcvv, $cardexpdate, $cardcurrency, $amount, $cardname, $billingname, $billingemail, $billingphone, $billingdob, $billingcompany, $billingaddress, $billingcity, $billingstate, $billingcountry, $billingpostalcode);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            $val = true;
        } else {
            $val = false;
        }

        if ($val) {
            echo 'success: Thank you, Your Purchase is being registered';        
        } else {
            echo 'success: message sent';
        }
    }
    else {
        echo 'error: Error Connecting to server Please try again';
    }

} else {
    echo 'error: Please Check Your Credit Card';
}

?>
