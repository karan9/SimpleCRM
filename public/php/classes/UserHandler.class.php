<?php 
/**
*
* A Simple Class That Encapsulates Database for me
*
*/

class UserHandler {
    
    private $conn;

    public function __construct() {
        require_once 'DB_Connect.class.php';
        // connecting to database
        $db = new Db_Connect();
        $this->conn = $db->connect();
    }

    private function getUniqueId($isAdmin, $isZorbaOffice) {
        $adminPrefix = 'AD';
        $clientPrefix = 'ZO';
        $apiPrefix = 'API';

        $randHash = mt_rand(5000, 10000);
        
        if ($isAdmin && $isZorbaOffice) {
            return $adminPrefix . $randHash;
        } else if (!$isAdmin && $isZorbaOffice) {
            return $clientPrefix . $randHash;
        } elseif (!$isAdmin && !$isZorbaOffice) {
            return $apiPrefix . $randHash;
        } else {
            return $randHash;
        }
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

    public function createUser($username, $password, $role, $isZorbaOffice) {
        // generate unique id
        if ($role == 'admin') {
            $uid = $this->getUniqueId(true, $isZorbaOffice);
        } else {
            $uid = $this->getUniqueId(false, $isZorbaOffice);
        }
        
        // send user
        $stmt = $this->conn->prepare("INSERT INTO users(uid, username, password, role, created_at) VALUES(?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $uid, $username, $password, $role);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function updateUser($username) {

    } 

    public function removeUser($username) {

    } 

    public function findUserByUsername($username) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result(); // store it some where
        $user = $this->fetchAssocStatement($stmt);
        $stmt->close();
 
        if ($user) {
            return $user;
        } else {
            return false;
        }
    }

    public function findUserByRole($role) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE role = ?");
        $stmt->bind_param("s", $role);
        $stmt->execute();
        $stmt->store_result(); // store it some where
        $user = $this->fetchAssocStatement($stmt);
        $stmt->close();
 
        if ($user) {
            return $user;
        } else {
            return false;
        }
    }
}
?>