<?php

// prepare your data...
noop::set( 'var/title', 'Welcome to Noop !' );
noop::set( 'var/content', 'This is a sample page.' );
noop::set( 'var/list', array(
	'key1'=>'value1',
	'key2'=>'value2'
) );

// ...prepare your view...
$view = noop::view( 'welcome', NULL, 3600 );

// ...then send it.
noop::output( $view, 'html' );
