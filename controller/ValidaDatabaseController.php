<?php
require_once 'db/DBconfig.php';

class ValidaDatabaseController {
    
    private $db;

    public function __construct() {
    }
     
    public function validaDB() {
       try {
           $DBConfig = new DBConfig();
           $this->db = $DBConfig->getDB();
           return TRUE;
       } catch (PDOException $e) {
           throw $e;
       }
    }
}
?>