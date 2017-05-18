<?php

// This is an usage example of the filesystem views cache

// activate globally cache
noop::set( 'config/cache/active', TRUE );

// iterations
$nb = 1000;

// prepare data...
noop::set( 'var/title', 'Welcome to Noop !' );
noop::set( 'var/subtitle', 'This is a sample page.' );
noop::set( 'var/lorem', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed congue placerat est, non semper ante fermentum iaculis. Integer viverra, nisi ut gravida hendrerit, ex risus finibus erat, non tempus lacus ipsum ac lectus. Suspendisse at egestas diam. Vestibulum molestie, felis eget tristique auctor, est ex iaculis est, ut ultricies ligula orci nec lacus. Ut ac posuere urna. Curabitur blandit tempus diam, et malesuada arcu luctus non. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Proin sed gravida justo.' );
noop::set( 'var/list', array(
	'key1'=>'value1',
	'key2'=>'value2',
	'key3'=>'value3',
) );

// bench without cache
noop::benchmark( 'nocache', TRUE );
for( $i=0; $i<$nb; $i++ )
	noop::view( 'welcome' );
noop::benchmark( 'nocache', FALSE );

// bench with cache
noop::benchmark( 'cache', TRUE );
for( $i=0; $i<$nb; $i++ )
	noop::view( 'welcome', NULL, 3600 ); // 3600s => 1h
noop::benchmark( 'cache', FALSE );

echo 'For '.$nb.' iterations'."\n\n";
echo 'Without cache: '.noop::benchmark( 'nocache' ).'s'."\n";
echo 'With cache: '.noop::benchmark( 'cache' ).'s'."\n\n";
echo 'Cache is stored in '.noop::get( 'config/path/cache' );

// ...then send it.
noop::output( NULL, 'text' );
