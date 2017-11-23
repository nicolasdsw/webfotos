<h2>Novo Usuário</h2>
<?php
if ( $errors ) {
    print '<ul class="errors">';
    foreach ( $errors as $field => $error ) {
        print '<li>'.htmlentities($error).'</li>';
    }
    print '</ul>';
}
?>
<form method="POST" action="?menu=novo-usuario&op=save">
    <label for="Login">Login:</label><br/>
    <input type="text" name="login" value="<?php print htmlentities($obj->login) ?>"/>
    <br/>
    <br/>
    <label for="email">E-mail:</label><br/>
    <input type="text" name="email" value="<?php print htmlentities($obj->email) ?>"/>
    <br/>
    <br/>
    <label for="novaSenha">Senha:</label><br/>
    <input type="password" name="novaSenha" value="" />
    <br/>
    <br/>
    <label for="confirmaSenha">Confirmar Senha:</label><br/>
    <input type="password" name="confirmaSenha" value="" />
    <br/>
    <br/>
    <input type="hidden" name="form-submitted" value="1" />
    <input type="submit" value="Salvar" />
    <input type="button" value="Limpar" onClick="self.location='index.php?menu=novo-usuario'" />
    <input type="button" value="Voltar para o Início" onClick="self.location='index.php'" />
</form>