<?php

// prepare your data...
noop::set( 'var/title', 'Welcome to Noop !' );
noop::set( 'var/subtitle', 'This is a sample page.' );
noop::set( 'var/lorem', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed congue placerat est, non semper ante fermentum iaculis. Integer viverra, nisi ut gravida hendrerit, ex risus finibus erat, non tempus lacus ipsum ac lectus. Suspendisse at egestas diam. Vestibulum molestie, felis eget tristique auctor, est ex iaculis est, ut ultricies ligula orci nec lacus. Ut ac posuere urna. Curabitur blandit tempus diam, et malesuada arcu luctus non. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Proin sed gravida justo.' );
noop::set( 'var/list', array(
	'key1'=>'value1',
	'key2'=>'value2',
	'key3'=>'value3',
) );

// ...prepare your page content view...
noop::set( 'var/page', noop::view( 'welcome/welcome' ) );

// ...put that content in a template page...
echo noop::view( 'welcome/page' );

// ...then send it.
noop::output( NULL, 'html' );
