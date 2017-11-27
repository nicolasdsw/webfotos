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
<div id="capa">
    <?php
    if ($obj->id != NULL) {
        echo '<img style="height: 200px;" src="albumGetImage.php?album-id=' . $obj->id . '"/>';
    }
    ?>
</div>
<div id="album-form">
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
        <label for="image">Capa do Álbum:</label>
    	<input type="file" name="fileUpload"/><br>
        <br>       	    
        <input type="hidden" name="form-submitted" value="1" />
        <input type="submit" value="Salvar" />
        <input type="button" value="Voltar para Álbuns" onClick="self.location='index.php?menu=<?php print htmlentities($menu) ?>'" /><br>
    </form>
</div>
<h3>Adicionar Imagens:</h3>
<form action="?menu=<?php print htmlentities($menu) ?>&op=add-uploads" method="post" multipart="" enctype="multipart/form-data">
	<input type="hidden" name="album-id" value="<?php print htmlentities($obj->id) ?>">
	<input type="file" name="imgs[]" multiple>
	<input type="submit">
</form>
Total de imagens: <?php print count($lista); ?>
<form method="POST" action="?menu=<?php print htmlentities($menu) ?>&op=delete-uploads&album-id=<?php print $obj->id ?>">
	<input id="excluir-imagens-selecionadas" type="submit" name="submit" value="Excluir Imagens Selecionadas"/><br><br>
    <div id="album-imagens">
        <?php foreach ($lista as $item): ?>
            	<div>
            		<?php if ($item->id != NULL) {
            		    echo '<input type="checkbox" name="uploadsIds[]" value="'.$item->id.'" name="img'.$item->id.'" id="img'.$item->id.'" />';
                        echo '<label for="img'.$item->id.'"><img style="height: 200px;" src="getImage.php?upload-id='.$item->id.'"/></label>';
                        echo '<span><a href="?menu='.$menu.'&op=delete-upload&album-id='.$obj->id.'&upload-id='.$item->id.'">Excluir</a></span>';
            	    } ?>
            	</div>
        <?php endforeach; ?>
    </div>
</form>