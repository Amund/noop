<?php

/* TODO
inspect,check,p
*/

ini_set( 'display_errors', 1 );
require_once __DIR__.'/../noop.php';

class NoopTest extends PHPUnit_Framework_TestCase {
	
	function setUp() {}
	
	function tearDown() {}
	
	
	function testNoopLibraryMustBeLoaded() {
		$this->assertTrue( class_exists( 'noop' ), 'Noop library not loaded');
	}
	
	function testNoopClassHaveStructure() {
		$methods = array(
			'start','view','pdo', // core methods
			'get','set','del', // manipulates private $var registry (and associative arrays)
			'output','redirect','status', // http response
			'check','filter', // helpers
			'inspect','benchmark' // DEV tools
		);
		foreach( $methods as $method )
			$this->assertTrue( method_exists( 'noop', $method ), 'No '.$method.' method' );
		
		$this->assertClassHasStaticAttribute( 'var', 'noop' );
	}
	
	function testServerVariablesExists() {
		$this->assertArrayHasKey( 'SCRIPT_NAME', $_SERVER );
		$this->assertArrayHasKey( 'REQUEST_URI', $_SERVER );
		$this->assertArrayHasKey( 'HTTP_HOST', $_SERVER );
	}
	
}
