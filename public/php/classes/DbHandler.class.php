<?php 
require_once "../vendor/meekrodb.2.3.class.php";


class DbHandler {
    
    private $dbuser, $dbpasswd, $dbname;

    public function __construct($dbuser, $dbpasswd, $dbname) {
        $this->$dbuser = $dbuser;
        $this->$dbpasswd = $dbpasswd;
        $this->$dbname = $dbname;
    }

    public function connect() {
        DB::$user = $this->$dbuser;
        DB::$password = $this->$dbpasswd;
        DB::$dbName = $this-> $dbname;
    }

    public function find($query) {
        $ret = DB::query($query);
    }

    /**
     *  - main @param: assoc_array = something
     *  @param $tblname = denotes table name
     *  @param $data: assoc_array = denotes data to be inserted or updated
     *  @param $where: selecttion cireteria
     **/

     // CREATE CRUD
}
?>