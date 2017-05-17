<?php

// prepare your data...
noop::set( 'var/title', 'Welcome to Noop !' );
noop::set( 'var/content', 'This is a sample page.' );
noop::set( 'var/list', array(
	'key1'=>'value1',
	'key2'=>'value2'
) );

$nb = 1000;
noop::benchmark( 'sanscache', TRUE );
for( $i=0; $i<$nb; $i++ )
	noop::view( 'welcome' );
noop::benchmark( 'sanscache', FALSE );
noop::benchmark( 'aveccache', TRUE );
for( $i=0; $i<$nb; $i++ )
	noop::view( 'welcome', NULL, 3600 );
noop::benchmark( 'aveccache', FALSE );

echo 'Pour '.$nb.' itérations'."\n";
echo 'Sans cache: '.noop::benchmark( 'sanscache' )."\n";
echo 'Avec cache: '.noop::benchmark( 'aveccache' );


// ...then send it.
noop::output( NULL, 'text' );
