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
            if ( !$op || $op == 'save') {
                $this->main();
            } else {
                $this->showError("Page not found", "Page for operation ".$op." was not found!");
            }
        } catch ( Exception $e ) {
            $this->showError("Application error", $e->getMessage());
        }
    }
    
    public function main() {
        $errors = array();         
        $obj = new Album(NULL);
        $menu = $this->menu;
        //Verifica se o botão salvar do formulário foi clicado       
        $errors = array();        
        if ( isset($_POST['form-submitted']) ) { 
            $obj = new Album(NULL);          
            $obj->id    = NULL;          
            $obj->name = isset($_POST['name'])   ?   $_POST['name'] : NULL;
            $obj->description = isset($_POST['description'])   ?   $_POST['description'] : NULL;
                   
            try {
                $res = $this->service->save($obj);
                echo "<br>";
                if ($res) {
                    echo "Novo álbum cadastrado com sucesso!";
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