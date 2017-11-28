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
<a href="?menu=<?php echo $menu ?>&op=new">Adicionar Álbum</a>  <br>
Total de álbuns: <?php print count($lista); ?> 
<br>
    <?php foreach ($lista as $item): ?>
        <div class="capa-album">
        	<span><?php print htmlentities($item->name); ?></span>
	   		<a href="?menu=<?php echo $menu ?>&op=show&id=<?php print $item->id; ?>">
        	<div>
        		<?php if ($item->id != NULL) {
        		    echo '<img class="img-item" src="data:'.$item->image_type.';base64,'. base64_encode($item->image). '" />';
        	    } ?>
        	</div>
	        </a>
        	<span><?php print htmlentities($item->description); ?></a></span><br>
        </div>
    <?php endforeach; ?>
<br /><br />