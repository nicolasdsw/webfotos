<br></br>
Total de registros: <?php print count($lista); ?>
<table class="mytable login" border="0" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th><a href="?menu=usuarios&orderby=login&dir=<?php print $dir * -1; ?>">Nome</a></th>
            <th><a href="?menu=usuarios&orderby=email&dir=<?php print $dir * -1; ?>">E-mail</a></th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($lista as $item): ?>
        <tr>
            <td><?php print htmlentities($item->login); ?></a></td>
            <td><?php print htmlentities($item->email); ?></a></td>
            <td><a href="?menu=usuarios&op=show&id=<?php print $item->id; ?>">editar</a>
            <a href="?menu=usuarios&orderby=<?php print $orderby; ?>&dir=<?php print $dir; ?>&delete=<?php print $item->id; ?>">delete</a></td>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<br /><br />