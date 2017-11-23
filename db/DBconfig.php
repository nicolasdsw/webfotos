<?php
require 'db/DB.class.php';



class DBConfig {

    private $host = "localhost:3306";
    private $user = "root";
    private $password = "";
    private $database = "webfotos";
    
    public static $db;

    public function __construct() {
        if ( self::$db == NULL) {
           $this->buildBD();            
        }
    }

    private function buildBD() {
        try {
            self::$db = new DB($this->user, $this->password, $this->database, $this->host);
        } catch (PDOException $e) {
            $nome_do_arquivo = "db/webfotos.sql";
            $arquivo = Array();
            $arquivo = file($nome_do_arquivo);
            $preparaSQL = "";
            foreach ($arquivo as $v) {
                $preparaSQL .= $v;
            }
            try {
                //$sql = explode(";", $prepara);
                $dbh = new PDO("mysql:host=".$this->host, $this->user, $this->password);
                $dbh->exec($preparaSQL);                
                self::$db = new DB($this->user, $this->password, $this->database, $this->host);
            } catch (PDOException $e) {
                throw $e;
            }
        }
    }
    
    public function getDB() {
        return self::$db;
    }
}
?>
