<?php
require 'model/Album.php';
require 'model/Upload.php';
require_once 'service/AlbumService.php';

class AlbumController {
    
    private $service = NULL;
    private $model = "Album";
    private $view = "view/album";
    private $menu = "albuns";

    public function __construct() {
        $this->service = new AlbumService();
    }
     
    public function showError($title, $message) {
        include 'view/error.php';
    }
    
    public function handleRequest() {
        $op = isset($_GET['op'])?$_GET['op']:NULL;
        try {
            if ( !$op ) {
                $this->listar();
            } else if ( $op == 'new' ) {
                $this->newAlbum();
            } else if ( $op == 'show' ) {
                $this->show();
            } else if ( $op == 'save' ) {
                $this->save();
            } else if ( $op == 'add-uploads' ) {
                $this->addUploads();
            } else if ( $op == 'delete-upload' ) {
                $this->deleteUpload();
            } else if ( $op == 'delete-uploads' ) {
                $this->deleteUploads();
            } else {
                $this->showError("Page not found", "Page for operation ".$op." was not found!");
            }
        } catch ( Exception $e ) {
            $this->showError("Application error", $e->getMessage());
        }
    }
    
    public function listar() {
        $errors = array();
        $menu = $this->menu;
        $title = 'Meus Álbuns';
        //atualiza os registros da tabela
        $orderby = isset($_GET['orderby'])?$_GET['orderby']:NULL;
        $dir = isset($_GET['dir'])?$_GET['dir']:1;
        if ($dir == 0) $dir = 1;
        $direction = ($dir > 0) ? "ASC" : "DESC";
        $lista = $this->service->getAll($orderby, $direction);
        
        //inclui o front-end
        include $this->view.'/albums.php';        
    }   

    public function newAlbum() {
        $errors = array();
        $menu = $this->menu;
        $title = 'Cadastrar Álbum';
        $obj = new $this->model(NULL); 
        //inclui o front-end
        include $this->view.'/new.php';        
    }   

    public function show() {
        $errors = array();         
        $menu = $this->menu;
        $title = 'Álbum ';
        $lista = NULL;
        //verifica se deve deletar um registro
        $deleteId = isset($_GET['delete'])?$_GET['delete']:NULL;
        if ( $deleteId ) {
            try {
                $this->service->delete($deleteId);
            } catch (ValidationException $e) {
                $errors = $e->getErrors();
            }
        }

        $id = isset($_GET['id'])?$_GET['id']:NULL;
        if ( !$id ) {
            header("location: index.php?menu=".$menu."&op=new");
        } else {
            $obj = $this->service->getById($id);          
            if ( !$obj ) {
                throw new Exception('Registro não existe.');
            }
            $title = 'Álbum '.$obj->name;
            $lista = $obj->uploads;
        }
        include $this->view.'/show.php';
    }

    public function save() {
        $errors = array();
        $menu = $this->menu;
        $title = 'Cadastrar Álbum';
        
        //Verifica se o botão salvar do formulário foi clicado
        $errors = array();
        if ( isset($_POST['form-submitted']) ) {
            $id = isset($_POST['id'])      ?   $_POST['id']  : NULL;
            $obj = $this->service->getById($id);            
            $obj->name = isset($_POST['name'])   ?   $_POST['name'] : NULL;
            $obj->description = isset($_POST['description'])   ?   $_POST['description'] : NULL;           
            $imgs = $_FILES['fileUpload'];
            print $imgs;
            if(!empty($imgs) && $imgs['tmp_name'] != NULL) {
                $obj->image = file_get_contents($imgs['tmp_name']);
                $obj->image_type = $imgs['type'];
            }
            try {
                $res = $this->service->save($obj);
                echo "<br>";
                if ($res) {
                    echo "Álbum salvo com sucesso!";
                }
            } catch (ValidationException $e) {
                $errors = $e->getErrors();
            }
        }               
        if (!empty($errors) || $obj->id == NULL) {
            include $this->view.'/new.php';            
        } else {
            header("location: index.php?menu=".$menu."&op=show&id=".$obj->id);
        }
    }
    
    public function addUploads() {
        $errors = array();
        $menu = $this->menu;
        $title = 'Álbum ';
        $lista = NULL;        
        $imgs = $_FILES['imgs'];
        $albumId = isset($_POST['album-id']) ?   $_POST['album-id']  : NULL;
        if(!empty($imgs) && $imgs['tmp_name'][0] != NULL && $albumId != NULL) {         
            $total = count($imgs['name']);
            $uploadsList = array();
            for($i=0; $i < $total; $i++) {
                $upload = new Upload(NULL);
                $upload->file = file_get_contents($imgs['tmp_name'][$i]);
                $upload->file_type = $imgs['type'][$i];
                $upload->id_album = $albumId;
                array_push($uploadsList, $upload);
            }            
            if (!$this->service->addUploads($uploadsList)) {
                print "Erro durante o upload dos arquivos.";
            }
        }
        header("location: index.php?menu=".$menu."&op=show&id=".$albumId);
    }
    
    public function deleteUpload() {
        $errors = array();
        $menu = $this->menu;
        $albumId = isset($_GET['album-id']) ?   $_GET['album-id']  : NULL;
        //verifica se deve deletar um registro
        $uploadId = isset($_GET['upload-id'])?$_GET['upload-id']:NULL;
        if ( $albumId && $uploadId ) {
            try {
                $this->service->deleteUpload($uploadId);
            } catch (ValidationException $e) {
                $errors = $e->getErrors();
            }
        }
        
        if (!empty($errors)) {
            include $this->view.'/show.php';
        } else {
            header("location: index.php?menu=".$menu."&op=show&id=".$albumId);
        }
    }

    public function deleteUploads() {
        $errors = array();
        $menu = $this->menu;
        $albumId = isset($_GET['album-id']) ?   $_GET['album-id']  : NULL;
        
        if(!empty($_POST['uploadsIds'])) {
            $this->service->deleteUploads($_POST['uploadsIds']);
        }
        if (!empty($errors)) {
            include $this->view.'/show.php';
        } else {
            header("location: index.php?menu=".$menu."&op=show&id=".$albumId);
        }
    }
}
?>