<?php

class NoopViewTest extends PHPUnit_Framework_TestCase {
	
	function tearDown() {
		noop::set( 'config/path/controller', 'secure/control/' );
	}
	
	
	function testView01() {
		$path =  __DIR__.'/fixtures';
		noop::set( 'config/path/view', $path );
		$output = noop::view( 'view01', NULL, TRUE );
		$this->assertEquals( 'view', $output );
	}
	
	function testView02() {
		$path =  __DIR__.'/fixtures';
		noop::set( 'config/path/view', $path );
		$output = noop::view( 'view02', 'view', TRUE );
		$this->assertEquals( 'view', $output );
	}
	
	function testView03() {
		$path =  __DIR__.'/fixtures';
		noop::set( 'config/path/view', $path );
		$data = array( 'v','i','e','w' );
		$output = noop::view( 'view03', $data, TRUE );
		$this->assertEquals( 'view', $output );
	}
	
}
