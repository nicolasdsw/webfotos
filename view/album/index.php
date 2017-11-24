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
	<input type="file" name="image"/><br>
	<?php 
	if ($obj->image != NULL) {
		echo '<img style="height: 100px;" src="imageView.php?album-id='.$obj->id.'"/>';
	}
	?>
    <br>
    <input type="hidden" name="form-submitted" value="1" />
    <input type="submit" value="Salvar" />
    <input type="button" value="Limpar" onClick="self.location='index.php?menu=<?php print htmlentities($menu) ?>'" />
    <input type="button" value="Voltar para o Início" onClick="self.location='index.php'" />
</form>

<br></br>
Total de registros: <?php print count($lista); ?>
<table class="mytable login" border="0" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th><a href="?menu=<?php echo $menu ?>&orderby=name&dir=<?php print $dir * -1; ?>">Nome</a></th>
            <th><a href="?menu=<?php echo $menu ?>&orderby=description&dir=<?php print $dir * -1; ?>">Descrição</a></th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($lista as $item): ?>
        <tr>
            <td><?php print htmlentities($item->name); ?></a></td>
            <td><?php print htmlentities($item->description); ?></a></td>
            <td><a href="?menu=<?php echo $menu ?>&op=show&id=<?php print $item->id; ?>">editar</a>
            <a href="?menu=<?php echo $menu ?>&orderby=<?php print $orderby; ?>&dir=<?php print $dir; ?>&delete=<?php print $item->id; ?>">delete</a></td>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<br /><br />