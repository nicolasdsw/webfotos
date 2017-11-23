<?php
if ( $errors ) {
    print '<ul class="errors">';
    foreach ( $errors as $field => $error ) {
        print '<li>'.htmlentities($error).'</li>';
    }
    print '</ul>';
}
?>
<form method="POST" action="?menu=usuarios&op=save">
    <input type="hidden" name="id" value="<?php print htmlentities($obj->id) ?>" />
    <input type="hidden" name="password" value="<?php print htmlentities($obj->password) ?>" />
    <label for="Login">Login:</label><br/>
    <input type="text" name="login" value="<?php print htmlentities($obj->login) ?>"/>
    <br/>
    <br/>
    <label for="email">E-mail:</label><br/>
    <input type="text" name="email" value="<?php print htmlentities($obj->email) ?>"/>
    <br/>
    <br/>
    <label for="salvarSenha">Alterar a Senha:</label>
    <input type="checkbox" onClick="exibeCampoSenha()" name="salvarSenha" value="<?php print htmlentities($obj->salvarSenha) ?>" />   
    <br/>
    <br/>
    <div id="novaSenha" style="display: none">
        <label for="novaSenha">Nova Senha:</label><br/>
        <input type="password" name="novaSenha" value="<?php print htmlentities($obj->novaSenha) ?>" />
        <br/>
        <br/>
    </div>
    <input type="hidden" name="form-submitted" value="1" />
    <input type="submit" value="Salvar" />
    <input type="button" value="Limpar" onClick="self.location='index.php?menu=usuarios'" />
</form>

<script type="text/javascript">
    function exibeCampoSenha() {
        var display = document.getElementById("novaSenha").style.display;
        if(display == "none")
            document.getElementById("novaSenha").style.display = 'block';
        else
            document.getElementById("novaSenha").style.display = 'none';

    } 
</script>