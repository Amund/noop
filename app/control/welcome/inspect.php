<?php

// prepare your data...
noop::set( 'var/title', 'Inspect a request' );

// ...prepare your page content view...
noop::set( 'var/page', noop::view( 'welcome/inspect' ) );

// ...put that content in a template page...
echo noop::view( 'welcome/page' );

// ...then send it.
noop::output( NULL, 'html' );
