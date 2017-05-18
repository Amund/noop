
<h1><?=noop::get( 'var/title' )?></h1>
<h2><?=noop::get( 'var/subtitle' )?></h2>
<p><?=noop::get( 'var/lorem' )?></p>

<table>
<?php foreach( noop::get( 'var/list' ) as $key=>$value ) { ?>
	<tr>
		<th><?=$key?></th><td><?=$value?></td>
	</tr>
<?php } ?>
</table>

<h2>How it works ?</h2>
<p>This page is a compilation of a template page (app/view/welcome/welcome.php) and some data. You can see these raw data from a <code>noop::inspect( 'var' )</code>, which result in :</p>
<?=noop::inspect( 'var' )?>
<p>Want too see more ? Check <a href="welcome/inspect?with=some&random=parameters">all available data</a> for a request.</p>
<p>Or dive into <a href="welcome/help">help</a> to learn the magic.</p>
