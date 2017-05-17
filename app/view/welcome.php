<!DOCTYPE html>
<html>
<head>
<title><?=noop::get( 'var/title' )?></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0" />
</head>
<body>

<h1><?=noop::get( 'var/title' )?></h1>
<p><?=noop::get( 'var/content', $data )?></p>

<hr>

<table border="1">
<?php foreach( noop::get( 'var/list' ) as $key=>$value ) { ?>
	<tr>
		<th><?=$key?></th>
		<td><?=$value?></td>
	</tr>
<?php } ?>
</table>

<hr>

<?=noop::inspect( 'var' )?>
<p>See <a href="inspect?with=some&random=parameters">all available data</a> for a request</p>
<hr>

<p>Need <a href="noop/help">help</a> ?</p>

<?=noop::inspect()?>
</body>
</html>