<?php
if ( $errors ) {
    print '<ul class="errors">';
    foreach ( $errors as $field => $error ) {
        print '<li>'.htmlentities($error).'</li>';
    }
    print '</ul>';
}
?>
<div id="loginPage">
    <form method="POST" action="?menu=login&op=login">
        <label for="Login">Login:</label><br/>
        <input type="text" name="login" value="<?php print htmlentities($login) ?>"/>
        <br/>
        <br/>
        <label for="password">Senha:</label><br/>
        <input type="password" name="password" value="<?php print htmlentities($password) ?>" />
        <br/>
        <br/>
        <input type="hidden" name="form-submitted" value="1" />
        <input type="submit" value="Entrar" />
        <input type="button" value="Criar conta" onClick="self.location='index.php?menu=novo-usuario'"/>
    </form>
</div>