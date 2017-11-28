<?php
require 'model/Usuario.php';
require_once 'service/AuthService.php';

class AuthController {
    
    private $service = NULL;
    private $model = "Usuario";
    private $view = "view/auth";
    private $menu = "auth";

    public function __construct() {
        $this->service = new AuthService();
    }
     
    public function showError($title, $message) {
        include 'view/error.php';
    }
    
    public function handleRequest() {
        $op = isset($_GET['op'])?$_GET['op']:NULL;
        try {
            if ( !$op || $op == 'login' ) {
                $this->main();
            } else if ( $op == 'logout' ) {
                session_destroy();
                unset($_SESSION["login"]);
                unset($_SESSION["admin"]);
                unset($_SESSION["user_id"]);
                header("location: index.php");
            } else {
                $this->showError("Page not found", "Page for operation ".$op." was not found!");
            }
        } catch ( Exception $e ) {
            $this->showError("Application error", $e->getMessage());
        }
    }
    
    public function main() {
        $errors = array();         
        
        //Verifica se o botão login do formulário foi clicado       
        $login = NULL;
        $password = NULL;

        if ( isset($_POST['form-submitted']) ) {            
            $login = isset($_POST['login'])   ?   $_POST['login'] : NULL;
            $password = isset($_POST['password'])   ?   $_POST['password'] : NULL;
            $usuario = NULL;
            try {
                $usuario = $this->service->getByLoginAndSenha($login, $password);
                if ($usuario != NULL) {
                    $_SESSION["login"] = $usuario->login;
                    $_SESSION["admin"] = $usuario->superuser;
                    $_SESSION["user_id"] = $usuario->id;
                    header("location: index.php");
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