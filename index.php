<html>
    <head>
         <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
         <link rel="stylesheet" type="text/css" href="view/style.css">
    </head>
    <body>
        <h1>WEB FOTOS</h2>
        <header style="margin-left: 6%; margin-right: 6%;">
            <?php 
            session_start();
            $usuarioLogin = "";
            $menu = isset($_GET['menu'])?$_GET['menu']:NULL;
            
            if (isset($_SESSION["login"])) { ?>
                <h3>MENUS - Selecione uma das opções:</h3>
                <?php if ($_SESSION["admin"]) { ?>
                    <input type="button" value="Gerenciar Usuários" onClick="self.location='index.php?menu=usuarios'" />
                	&nbsp
               	<?php } ?>
                <input type="button" value="Gerenciar Álbuns" onClick="self.location='index.php?menu=albuns'" />
                &nbsp
                <input type="button" value="Sair (Logout)" onClick="self.location='index.php?menu=login&op=logout'" />
            <?php 
                $usuarioLogin = $_SESSION["login"];
                echo "<br><h4>Olá, ".$usuarioLogin."</h4>";
            }
            ?>
        </header>
        <main style="margin-left: 6%; margin-right: 6%;">
        <?php
            try {                
                if ($menu == NULL && !isset($_SESSION["login"])) {
                    require_once  'controller/ValidaDatabaseController.php';
                    $controller = new ValidaDatabaseController();
                   try {
                       $controller->validaDB();
                    } catch (Exception $e) {
                       echo "Verifique o usuário, a senha e o endereço de acesso do banco de dados em db/DBconfig.php <br>";
                       print htmlentities($e->getMessage());
                   }           
                }
                
                if (!isset($_SESSION["login"])) {
                    if ( $menu == 'novo-usuario' ) {
                        require_once 'controller/NovoUsuarioController.php';
                        $controller = new NovoUsuarioController();
                        $controller->handleRequest();
                    } else {
                        $menu = "login";                        
                    }
                }
                
                if ( $menu == 'login' ) {
                    require_once 'controller/AuthController.php';
                    $controller = new AuthController();
                    $controller->handleRequest();
                } elseif ( $menu == 'usuarios' && $_SESSION["admin"]) {
                    require_once 'controller/UsuarioController.php';
                    $controller = new UsuarioController();
                    $controller->handleRequest();
                } elseif ( $menu == 'albuns' ) {
                    require_once 'controller/AlbumController.php';
                    $controller = new AlbumController();
                    $controller->handleRequest();
                }
            } catch ( Exception $e ) {
                print htmlentities($e->getMessage());
            }
        ?>
        </main>
    </body>
</html>
