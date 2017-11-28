<?php
require 'model/Usuario.php';
require_once 'service/UsuarioService.php';

class UsuarioController {
    
    private $service = NULL;
    private $model = "Usuario";
    private $view = "view/usuario";
    private $menu = "usuarios";

    public function __construct() {
        $this->service = new UsuarioService();
    }
     
    public function showError($title, $message) {
        include 'view/error.php';
    }
    
    public function handleRequest() {
        $op = isset($_GET['op'])?$_GET['op']:NULL;
        try {
            if ( !$op || $op == 'show' || $op == 'new' || $op == 'save' ) {
                $this->main();
            } else {
                $this->showError("Page not found", "Page for operation ".$op." was not found!");
            }
        } catch ( Exception $e ) {
            $this->showError("Application error", $e->getMessage());
        }
    }
    
    public function main() {
        //controle de mensagens de erros
        $errors = array();      

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
            $obj->login = isset($_POST['login'])   ?   $_POST['login'] : NULL;
            $obj->email = isset($_POST['email'])   ?   $_POST['email'] : NULL;
            $obj->superuser = isset($_POST['superuser']);
            $obj->password = isset($_POST['password'])   ?   $_POST['password'] : NULL;
            $obj->salvarSenha = isset($_POST['salvarSenha']);
            $obj->novaSenha = isset($_POST['novaSenha'])   ?   $_POST['novaSenha'] : NULL;
            
            try {
                $res = $this->service->save($obj);
                echo "<br>";
                if ($res) {
                    echo "Registro salvo com sucesso!";
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
