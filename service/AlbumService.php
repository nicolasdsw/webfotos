<?php
require_once 'db/DBconfig.php';
require_once 'service/ValidationException.php';
require_once 'service/UploadService.php';
require_once 'model/Album.php';
require_once 'utils/IfUtils.php';

class AlbumService {
    
    private $db;
    private $table = "albums";
    private $model = "Album";
    private $primaryKey = "id_album";
    private $uploadService = NULL;
    
    public function __construct() {
        $DBConfig = new DBConfig();
        $this->db = $DBConfig->getDB();        
        $this->uploadService = new UploadService();
    }

    public function getAll($order, $direction) {
        $userId = $_SESSION["user_id"];
        return $this->getByUserId($userId, $order, $direction);
    }

    public function getByUserId($userId, $order, $direction) {
        try {
            if (!isset($order) || $order == NULL) {
                $order = $this->primaryKey;
            }
            if ( !isset($direction) ) {
                $direction = "ASC";
            }
            $list = array();
            $res = $this->db->preparedQuery("SELECT * FROM $this->table WHERE id_user=? ORDER BY $order $direction;", array($userId));
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
            if ($obj != NULL) {
                $obj->uploads = $this->uploadService->getByAlbumId($obj->id, NULL, NULL);                
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
    
    private function getImage( $albumId ) {        
        $name = null;
        $lob = null;
        $image_type = null;
    	$stmt = $this->db->prepare("SELECT name, image, image_type from ".$this->table." where ".$this->primaryKey."=?");
    	$stmt->execute(array($albumId));
    	$stmt->bindColumn(1, $name, PDO::PARAM_STR, 256);
    	$stmt->bindColumn(2, $lob, PDO::PARAM_LOB);
    	$stmt->bindColumn(3, $type, PDO::PARAM_STR, 256);
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
            $parameters['name'] = $obj->name;
            $parameters['description'] = $obj->description;
            $parameters['image'] = $obj->image;
            $parameters['image_type'] = $obj->image_type;
            $parameters['id_user'] = $obj->id_user;
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
        if (isEmpty($obj->name)) {
            $errors[] = 'Campo nome deve ser informado';
        }

        if ($obj->image_type != NULL) {
            $allowed = array("image/jpeg", "image/gif", "image/png");
            if(!in_array($obj->image_type, $allowed)) {
                $errors[] = 'Apenas arquivos dos tipos jpg, gif, and png são permitidos.';
            }
        }        
        
        $obj->id_user = $_SESSION["user_id"];
        if ( empty($errors) ) {
            return;
        }
        throw new ValidationException($errors);
    }
    
    public function addUploads($uploadsList) {
        return $this->uploadService->addUploads($uploadsList);
    }

    public function deleteUpload($id) {
        return $this->uploadService->delete($id);
    }

    public function deleteUploads($uploadsIds) {
        $this->uploadService->deleteUploads($uploadsIds);
    }
}
?>
