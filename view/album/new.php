<h2><?php echo $title; ?></h2>
<?php
if ( $errors ) {
    print '<ul class="errors">';
    foreach ( $errors as $field => $error ) {
        print '<li>'.htmlentities($error).'</li>';
    }
    print '</ul>';
}
?>
<form method="POST" action="?menu=<?php print htmlentities($menu) ?>&op=save" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php print htmlentities($obj->id) ?>" />
    <label for="name">Nome:</label><br/>
    <input type="text" name="name" value="<?php print htmlentities($obj->name) ?>"/>
    <br/>
    <br/>
    <label for="description">Descrição:</label><br/>
    <textarea name="description"><?php print htmlentities($obj->description) ?></textarea>
    <br/>
    <br/>
    <label for="image">Imagem:</label>
	<input type="file" name="fileUpload"/><br>
    <br>
    <input type="hidden" name="form-submitted" value="1" />
    <input type="submit" value="Salvar" />
    <input type="button" value="Voltar para Álbuns" onClick="self.location='index.php?menu=<?php print htmlentities($menu) ?>'" /><br>
</form>

<br /><br />