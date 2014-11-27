<!DOCTYPE html>
<html>
<head>
<title><?=noop::get( 'title', $data )?></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0" />
</head>
<body>

<h1><?=noop::get( 'title', $data )?></h1>
<p><?=noop::get( 'content', $data )?></p>

<hr>

<table border="1">
<?php foreach( noop::get( 'list', $data ) as $key=>$value ) { ?>
	<tr>
		<th><?=$key?></th>
		<td><?=$value?></td>
	</tr>
<?php } ?>
</table>

<hr>
<?=noop::inspect( '', $data )?>

</body>
</html>