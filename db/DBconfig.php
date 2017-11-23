<?php
require 'db/DB.class.php';
class DBConfig {
  
  public static $db;

  public function __construct() {
    self::$db = new DB( 'root', '', 'webfotos', 'localhost:3306');
  }
  public function getDB() {
    return self::$db;
  }
}
 ?>
