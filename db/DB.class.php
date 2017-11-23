<?php
class DB {

    private $database;
    public $transactionOpen = false;

    public function __construct($user, $password, $dbName, $dbServer = 'localhost') {
        $str = 'mysql:host=' . $dbServer . ';dbname=' . $dbName;
        try {
            $this->database = new PDO($str, $user, $password);
            $this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            print "Ocorreu um erro de conexão: " . $e->getMessage();
        }
    }

    public function __destruct() {
        $this->database = null;
    }

    public function begin() {
        if (!$this->transactionOpen) {
            $this->transactionOpen = true;
            return $this->database->beginTransaction();
        }
    }

    public function rollback() {
        $this->transactionOpen = false;
        return $this->database->rollBack();
    }

    public function commit() {
        if ($this->transactionOpen) {
            $this->transactionOpen = false;
            return $this->database->commit();
        }
    }

    public function preparedQuery($sql, $data) {
        try {
            $stmt = $this->database->prepare($sql);
            $stmt->execute($data);
            return $stmt->fetchAll();
        } catch (PDOException $exception) {
            echo '<pre>'.$exception->getMessage().$sql.'</pre>';
        }
    }

    public function execute($sql, $data) {
        try {
            $stmt = $this->database->prepare($sql);
            return $stmt->execute(array_values($data));
        } catch (PDOException $exception) {
            echo '<pre>'.$exception->getMessage().$sql.'</pre>';
            return false;
        }
    }

    public function lastInsertId($name){
        return $this->database->lastInsertId($name);
    }

    public function insert($tabela, $valores) {
        $colunas = "";
        $vars = "";
        $sql = "INSERT INTO ".$tabela."(";
        foreach ($valores as $key => $value) {
            $colunas = $colunas.$key.", ";
            $vars = $vars."?, ";
        }
        $colunas = substr($colunas, 0, -2);
        $vars = substr($vars, 0, -2);
        $sql = $sql.$colunas.") VALUES (".$vars.");";

        $parameters = array();
        foreach ($valores as $key => $value) {
            array_push($parameters, $value);
        }
       
        $lastInsertId = NULL;
        $stmt = $this->database->prepare($sql);
        if ($this->transactionOpen) {
            $res = $stmt->execute(array_values($parameters));
            if ($res) {
                $lastInsertId = $this->lastInsertId(NULL);
            }
        } else {
            $this->begin();
            $res = $stmt->execute(array_values($parameters));
            if ($res) {
                $lastInsertId = $this->lastInsertId(NULL);
            }
            $this->commit();
        }
        return $lastInsertId;
    }

    public function update($tabela, $valores, $where) {
        $sql = "UPDATE ".$tabela." SET ";
        $parameters = array();
        foreach ($valores as $key => $value) {
            $sql = $sql.$key."=?, ";
            array_push($parameters, $value);
        }
        $sql = substr($sql, 0, -2);
        $sql = $sql." WHERE ".$where;      
        return $this->execute($sql, $parameters);
    }

    public function delete($tabela, $where) {
        $sql = "DELETE FROM ".$tabela." WHERE ".$where;     
        return $this->execute($sql, array());
    }
}
