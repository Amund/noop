<?php

// Configure PHP
// setlocale( LC_ALL, array( 'fr_FR.UTF-8', 'fr_FR', 'fr' ) );
// ini_set( 'date.timezone', 'Europe/Paris' );
// header_remove( 'X-Powered-By' );
// spl_autoload_register( 'autoload' );

// Load library...
require 'noop.php';

// ...apply your config...
noop::config( array(
	'myparam'=>'myvalue'
) );

// ...then start the magic
noop::start();




// Simple but effective Autoload
function autoload( $class ) {
	$path = str_replace( '\\', DIRECTORY_SEPARATOR, $class );
	$path = noop::get( 'config/path/model' ).DIRECTORY_SEPARATOR.$path.'.php';
	if( !is_file( $path ) || !is_readable( $path ) )
		throw new NoopException( 'Class Not Found', 404 );
	require $path;
}