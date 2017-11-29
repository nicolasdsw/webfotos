<?php
require_once 'db/DBconfig.php';
require_once 'service/ValidationException.php';
require_once 'model/Usuario.php';
require_once 'utils/IfUtils.php';

class UsuarioService {
    
    private $db;
    private $table = "users";
    private $model = "Usuario";
    private $primaryKey = "id_user";
    
    public function __construct() {
        $DBConfig = new DBConfig();
        $this->db = $DBConfig->getDB();
    }

    public function getAll($order, $direction) {
        try {
            if (!isset($order) || $order == NULL) {
                $order = $this->primaryKey;
            }
            if ( !isset($direction) ) {
                $direction = "ASC";
            }
            $list = array();
            $res = $this->db->preparedQuery("SELECT * FROM $this->table ORDER BY $order $direction;", NULL);
            foreach ($res as $row) {
                $obj = new $this->model($row);
                array_push($list, $obj);
            }
            return $list;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getById($id) {
        try {
            $parameters = array();
            array_push($parameters, $id);
            $res = $this->db->preparedQuery("SELECT * FROM $this->table WHERE ".$this->primaryKey."=?", $parameters);
            $obj = NULL;
            foreach ($res as $row) {
                $obj = new $this->model($row);
            }
            return $obj;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getByLogin($login) {
        try {
            $parameters = array();
            array_push($parameters, $login);
            $res = $this->db->preparedQuery("SELECT * FROM $this->table WHERE login=?", $parameters);
            $obj = NULL;
            foreach ($res as $row) {
                $obj = new $this->model($row);
            }
            return $obj;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getByEmail($email) {
        try {
            $parameters = array();
            array_push($parameters, $email);
            $res = $this->db->preparedQuery("SELECT * FROM $this->table WHERE email=?", $parameters);
            $obj = NULL;
            foreach ($res as $row) {
                $obj = new $this->model($row);
            }
            return $obj;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function delete( $id ) {
        try {
            $obj = $this->getById($id);
            if (!$obj) {
                throw new ValidationException(array('Não foi possível encontrar o registro no banco de dados'));
            }
            $where = $this->primaryKey."=".$obj->id;
            $res = $this->db->delete($this->table, $where);
            if (!$res)
                $errors[] = 'Não foi possível deletar o registro';
            if ( empty($errors) ) return;
            throw new ValidationException($errors);
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function save( $obj ) {
        if ($obj != NULL ) {
            $this->beforeSave($obj); 
            $parameters = array();
            $parameters['login'] = $obj->login;
            $parameters['password'] = $obj->password;
            $parameters['email'] = $obj->email;
            $parameters['superuser'] = $obj->superuser;
            $where = $this->primaryKey."=".$obj->id; 
            if ($this->db->transactionOpen) {
                if ($obj->id != NULL) {
                    $res = $this->update($parameters, $where);
                } else {
                    $res = $this->insert($parameters);
                    $obj->id = $res;
                }
                return $res;
            } else {
                $this->db->begin();
                if ($obj->id != NULL) {
                    $res = $this->update($parameters, $where);
                } else {
                    $res = $this->insert($parameters);
                    $obj->id = $res;
                }
                $this->db->commit();
                return $res;
            }
        }
        return false;
    }

    private function insert( $parameters ) {
        try {
            $id = $this->db->insert($this->table, $parameters);
            if ($id == NULL) {
                $errors[] = 'Erro desconhecido ao cadastrar - Tente novamente mais tarde!';
                throw new ValidationException($errors);
            }
        } catch (Exception $e) {
            $id=NULL;
            $this->db->rollback();
            throw $e;
        }
        return $id;
    }

    private function update( $parameters, $where ) {
        try {
            $res = $this->db->update($this->table, $parameters, $where);
            if (!$res) {
                $errors[] = 'Erro desconhecido ao atualizar!';
                throw new ValidationException($errors);
            }
            return $res;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    private function beforeSave( $obj ) {
        $errors = array();
        if ( !isset($obj) || $obj == NULL ) {
            $errors[] = 'Objeto não definido';
        }
        if ( $obj->superuser == NULL ) {
            $obj->superuser = FALSE;
        }
        if (isEmpty($obj->login)) {
            $errors[] = 'Campo login deve ser informado';
        }
        if (isEmpty($obj->email)) {
            $errors[] = 'Campo e-mail deve ser informado';
        }
        if ($obj->salvarSenha) {
            if (isEmpty($obj->novaSenha)) {
                $errors[] = 'Campo senha deve ser informado';
            }
            $options = [
                'cost' => 11,
                'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
            ];
            $obj->password =  password_hash($obj->novaSenha, PASSWORD_BCRYPT, $options);
        }
        if (isEmpty($obj->password)) {
            $errors[] = 'Campo senha deve ser informado';
        }

        $res0 = $this->getByLogin($obj->login);
        if ($res0 != NULL && $res0->id != $obj->id) {
            $errors[] = 'Erro: o nome de usuário (login) já foi cadastrado!';
        }

        $res1 = $this->getByEmail($obj->email);
        if ($res1 != NULL && $res1->id != $obj->id) {
            $errors[] = 'Erro: o email do usuário já foi cadastrado!';
        }

        if ( empty($errors) ) {
            return;
        }
        throw new ValidationException($errors);
    }
}
?>
