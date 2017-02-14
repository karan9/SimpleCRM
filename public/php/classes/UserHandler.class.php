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

    private function getUniqueId() {
        $clientPrefix = 'ZO';
        $randHash = mt_rand(5000, 10000);

        return $clientPrefix . $randHash;
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

    public function createUser($username, $password, $role) {
        // generate unique id
        $uid = $this->getUniqueId();
        
        // send user
        $stmt = $this->conn->prepare("INSERT INTO users(uid, username, password, role, created_at) VALUES(?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $uid, $username, $password, $role);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return $result;
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