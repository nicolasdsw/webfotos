<?php
require 'model/Usuario.php';
require_once 'service/UsuarioService.php';

class NovoUsuarioController {
    
    private $service = NULL;
    private $model = "Usuario";
    private $view = "view/novo-usuario";
    private $menu = "novo-usuario";

    public function __construct() {
        $this->service = new UsuarioService();
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
        $obj = new Usuario(NULL);

        //Verifica se o botão salvar do formulário foi clicado       
        $errors = array();        
        if ( isset($_POST['form-submitted']) ) { 
            $obj = new Usuario(NULL);          
            $obj->id    = NULL;          
            $obj->login = isset($_POST['login'])   ?   $_POST['login'] : NULL;
            $obj->email = isset($_POST['email'])   ?   $_POST['email'] : NULL;
            $obj->password = NULL;
            $obj->salvarSenha = TRUE;
            $obj->novaSenha = isset($_POST['novaSenha'])   ?   $_POST['novaSenha'] : NULL;
            $obj->confirmaSenha = isset($_POST['confirmaSenha'])   ?   $_POST['confirmaSenha'] : NULL;
            
            if ($obj->novaSenha != $obj->confirmaSenha) {
                $errors[] = 'A senha informada deve ser igual a confirmação da senha!';
            } else {
                try {
                    $res = $this->service->save($obj);
                    echo "<br>";
                    if ($res) {
                        echo "Novo usuário cadastrado com sucesso!";
                    }
                } catch (ValidationException $e) {
                    $errors = $e->getErrors();
                }
            }
        }

        //inclui o front-end
        include $this->view.'/index.php';
    }
}
?>