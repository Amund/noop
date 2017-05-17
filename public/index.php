<?php

// Configure PHP
setlocale( LC_ALL, array( 'fr_FR.UTF-8', 'fr_FR', 'fr' ) );
ini_set( 'date.timezone', 'Europe/Paris' );
header_remove( 'X-Powered-By' );

// Load library...
require '../noop.php';

// ...apply your app config...
require '../config.php';

// ...then start the magic
noop::start();
