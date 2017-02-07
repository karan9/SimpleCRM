<?php 
class DbInfo {

    private $DB_NAME = ''; 
    private $DB_PASSWD = '';
    private $DB_USER= '';

    public function get_db_name() {
        return $this->$DB_NAME;
    }

    public function get_db_passwd() {
        return $this->$DB_PASSWD;
    }

    public function get_db_user() {
        return $this->$DB_USER;
    }
}
?>