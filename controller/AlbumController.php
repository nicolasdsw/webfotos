<?php
require 'model/Album.php';
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
            if ( !$op || $op == 'show' || $op == 'new' || $op == 'save' ) {
                $this->main();
            } else if ($op == 'get-image') {
                $id = $_GET['album-id'];
                if(isset($id)) {
                    $obj = $this->service->getById($id);
                    //header("Content-type: image/jpg");
                    echo $obj->image;
                }
            } else {
                $this->showError("Page not found", "Page for operation ".$op." was not found!");
            }
        } catch ( Exception $e ) {
            $this->showError("Application error", $e->getMessage());
        }
    }
    
    public function main() {
        $errors = array();         
        $menu = $this->menu;

        //verifica se deve deletar um registro
        $deleteId = isset($_GET['delete'])?$_GET['delete']:NULL;
        if ( $deleteId ) {
            try {
                $this->service->delete($deleteId);
            } catch (ValidationException $e) {
                $errors = $e->getErrors();
            }
        }
        
        //atualiza os registros da tabela
        $orderby = isset($_GET['orderby'])?$_GET['orderby']:NULL;
        $dir = isset($_GET['dir'])?$_GET['dir']:1;
        if ($dir == 0) $dir = 1;
        $direction = ($dir > 0) ? "ASC" : "DESC";
        $lista = $this->service->getAll($orderby, $direction);
        
        $id = isset($_GET['id'])?$_GET['id']:NULL;
        if ( !$id ) {
            $title = 'Inserir Registro';
            $obj = new $this->model(NULL);
        } else {
            $title = 'Editar Registro';
            $obj = $this->service->getById($id);
            if ( !$obj ) {
                throw new Exception('Registro não existe.');
            }
        }
        
        //Verifica se o botão salvar do formulário foi clicado       
        $errors = array();        
        if ( isset($_POST['form-submitted']) ) { 
            $obj->id    = isset($_POST['id'])      ?   $_POST['id']  : NULL;
            $obj->name = isset($_POST['name'])   ?   $_POST['name'] : NULL;
            $obj->description = isset($_POST['description'])   ?   $_POST['description'] : NULL;
             
           /*  $imagem = $_FILES["image"];
            if($imagem != NULL) {
                $nomeFinal = time().'.jpg';
                if (move_uploaded_file($imagem['tmp_name'], $nomeFinal)) {
                    $tamanhoImg = filesize($nomeFinal);
                    $mysqlImg = addslashes(fread(fopen($nomeFinal, "r"), $tamanhoImg));
                    $obj->image = $mysqlImg;
                }
            }
            else {
                echo"Você não realizou o upload de forma satisfatória.";
            } */ 
            
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if($check !== false){
                $image = $_FILES['image']['tmp_name'];
                $imgContent = addslashes(file_get_contents($image));
                $tamanhoImg = filesize($image);
                $obj->image = $imgContent;
            }
            try {
                $res = $this->service->save($obj);
                echo "<br>";
                if ($res) {
                    echo "Álbum salvo com sucesso!";
                    $lista = $this->service->getAll($orderby, $direction);
                }
            } catch (ValidationException $e) {
                $errors = $e->getErrors();
            }
        }

        //inclui o front-end
        include $this->view.'/index.php';
    }
}
?>