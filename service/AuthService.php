<?php
require_once 'db/DBconfig.php';
require_once 'service/ValidationException.php';
require_once 'model/Usuario.php';
require_once 'utils/IfUtils.php';

class AuthService {
    
    private $db;
    private $table = "users";
    private $model = "Usuario";
    private $primaryKey = "id_user";
    
    public function __construct() {
        $DBConfig = new DBConfig();
        $this->db = $DBConfig->getDB();
    }

    public function getByLoginAndSenha($login, $password) {
        try {
            if (isEmpty($login)) {
                $errors[] = 'O login deve ser informado';
            }
            if (isEmpty($password)) {
                $errors[] = 'A senha deve ser informada';
            }
            if ( !empty($errors) ) {
                throw new ValidationException($errors);
            }
           
            $parameters = array();
            array_push($parameters, $login);
            $res = $this->db->preparedQuery("SELECT * FROM $this->table WHERE login=?", $parameters);

            $obj = NULL;
            foreach ($res as $row) {
                $obj = new $this->model($row);
            }

            if ($obj == NULL || !password_verify($password, $obj->password)) {
                $errors[] = 'Erro de autenticação - Usuário ou Senha inválidos';
                throw new ValidationException($errors);
            }
            return $obj;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
?>
