<?php
require_once 'db/DBconfig.php';
require_once 'service/ValidationException.php';
require_once 'model/Upload.php';
require_once 'utils/IfUtils.php';

class UploadService {
    
    private $db;
    private $table = "uploads";
    private $model = "Upload";
    private $primaryKey = "id_upload";
    
    public function __construct() {
        $DBConfig = new DBConfig();
        $this->db = $DBConfig->getDB();
    }

    public function getAll($order, $direction) {
        $userId = $_SESSION["user_id"];
        return $this->getByUserId($userId, $order, $direction);
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

    public function getByAlbumId($albumId, $order, $direction) {
        try {
            if (!isset($order) || $order == NULL) {
                $order = $this->primaryKey;
            }
            if ( !isset($direction) ) {
                $direction = "ASC";
            }
            $list = array();
            $res = $this->db->preparedQuery("SELECT * FROM $this->table WHERE id_album=? ORDER BY $order $direction;", array($albumId));
            foreach ($res as $row) {
                $obj = new $this->model($row);
                array_push($list, $obj);
            }
            return $list;
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function addUploads($uploadsList) {
        $this->db->begin();
        try {            
            foreach($uploadsList as $obj) {
                $this->save($obj);
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
        return false;
    }

    public function deleteUploads($uploadsIds) {
        $checked_count = count($uploadsIds);
        if ($checked_count > 0) {
            $this->db->begin();        
            try {
                foreach($uploadsIds as $uploadId) {
                    if ( $uploadId ) {
                        $this->delete($uploadId);
                    }
                }
                $this->db->commit();
                return true;
            } catch (Exception $e) {
                $this->db->rollback();
                throw $e;
            }
        }
        return false;
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
    
    public function getImage( $uploadId ) {        
        $lob = null;
        $image_type = null;
    	$stmt = $this->db->prepare("SELECT file, file_type from ".$this->table." where ".$this->primaryKey."=?");
    	$stmt->execute(array($uploadId));
    	$stmt->bindColumn(1, $lob, PDO::PARAM_LOB);
    	$stmt->bindColumn(2, $type, PDO::PARAM_STR, 256);
    	$stmt->fetch(PDO::FETCH_BOUND);    	
    	header("Content-type: $type");
    	if (is_string($lob)) {   	    
    	    echo $lob;
    	} else {    	    
    	    fpassthru($lob); 
    	}    	
    }
    
    public function save( $obj ) {
        if ($obj != NULL ) {
            $this->beforeSave($obj); 
            $parameters = array();
            $parameters['file'] = $obj->file;
            $parameters['file_type'] = $obj->file_type;
            $parameters['id_album'] = $obj->id_album;
            $where = $this->primaryKey."=".$obj->id; 
            $res = NULL;
            if (!$this->db->transactionOpen) {
                if ($obj->id != NULL) {
                    $res = $this->update($parameters, $where);
                } else {
                    $res = $this->insert($parameters);
                    $obj->id = $res;
                }
            } else {
                $this->db->begin();
                if ($obj->id != NULL) {
                    $res = $this->update($parameters, $where);
                } else {
                    $res = $this->insert($parameters);
                    $obj->id = $res;
                }
                $this->db->commit();
            } 
	        return $res;
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
        
        if ($obj->file_type != NULL) {
            $allowed = array("image/jpeg", "image/gif", "image/png");
            if(!in_array($obj->file_type, $allowed)) {
                $errors[] = 'Apenas arquivos dos tipos jpg, gif, and png são permitidos.';
            }
        }
        
        if ( empty($errors) ) {
            return;
        }
        throw new ValidationException($errors);
    }
}
?>
