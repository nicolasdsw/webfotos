<h2>Meus Álbuns</h2>
<?php
if ( $errors ) {
    print '<ul class="errors">';
    foreach ( $errors as $field => $error ) {
        print '<li>'.htmlentities($error).'</li>';
    }
    print '</ul>';
}
?>
<form method="POST" action="?menu=<?php print htmlentities($menu) ?>&op=save">
    <label for="name">Nome:</label><br/>
    <input type="text" name="name" value="<?php print htmlentities($obj->name) ?>"/>
    <br/>
    <br/>
    <label for="description">Descrição:</label><br/>
    <textarea name="description" value="<?php print htmlentities($obj->description) ?>"></textarea>
    <br/>
    <br/>
    <input type="hidden" name="form-submitted" value="1" />
    <input type="submit" value="Salvar" />
    <input type="button" value="Limpar" onClick="self.location='index.php?menu=<?php print htmlentities($menu) ?>'" />
    <input type="button" value="Voltar para o Início" onClick="self.location='index.php'" />
</form>