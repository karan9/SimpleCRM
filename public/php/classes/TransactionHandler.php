<?php 

class TransactionHandler {
    private $conn;

    public function __construct() {
        require_once 'DB_Connect.class.php';
        // connecting to database
        $db = new Db_Connect();
        $this->conn = $db->connect();
    }

    private function getUniqueId($role) {
        $adminPrefix = 'AD';
        $randHash = mt_rand(5000, 10000);
        return $adminPrefix . $randHash;
    }

    private function fetchAssocStatement($stmt) {
        if($stmt->num_rows>0) {
            $result = array();
            $md = $stmt->result_metadata();
            $params = array();
            while($field = $md->fetch_field()) {
                $params[] = &$result[$field->name];
            }
            call_user_func_array(array($stmt, 'bind_result'), $params);
            if($stmt->fetch())
                return $result;
        }

        return null;
    }

    public function findTransactionById($uid)  {
        $stmt = $this->conn->prepare("SELECT * FROM transaction WHERE uid = ?");
        $stmt->bind_param("s", $uid);
        $stmt->execute();
        $stmt->store_result(); // store it some where
        $transaction = array();
        while($r = $this->fetchAssocStatement($stmt)){
            $transaction[] = $r;
        }
        $stmt->close();
 
        if ($transaction) {
            return $transaction;
        } else {
            return false;
        }
    }

    public function findTransactionByCard($cardNum) {
        $stmt = $this->conn->prepare("SELECT * FROM transaction WHERE card_number = ?");
        $stmt->bind_param("s", $cardNum);
        $stmt->execute();
        $stmt->store_result(); // store it some where
        $transaction = array();
        while($r = $this->fetchAssocStatement($stmt)){
            $transaction[] = $r;
        }
        $stmt->close();
 
        if ($transaction) {
            return $transaction;
        } else {
            return false;
        }
    }

    public function findTransactionByRange($startDate, $endDate) {
        
    }

    public function findTransactionBydate($date) {
        $stmt = $this->conn->prepare("SELECT * FROM transaction WHERE created_at = ?");
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $stmt->store_result(); // store it some where
        $transaction = array();
        while($r = $this->fetchAssocStatement($stmt)){
            $transaction[] = $r;
        }
        $stmt->close();
 
        if ($transaction) {
            return $transaction;
        } else {
            return false;
        }
    }

    public function findTransactions() {
        $stmt = $this->conn->prepare("SELECT * FROM transaction ORDER BY id DESC LIMIT  15");
        $stmt->execute();
        $stmt->store_result(); // store it some where
        $transaction = array();
        while($r = $this->fetchAssocStatement($stmt)) {
            $transaction[] = $r;
        }
        $stmt->close();
 
        if ($transaction) {
            return $transaction;
        } else {
            return false;
        }
    }
}
?>