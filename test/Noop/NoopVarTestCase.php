<?php

ini_set( 'display_errors', 1 );
require_once __DIR__.'/../noop.php';

class NoopVarTest extends PHPUnit_Framework_TestCase {
	
	function testNoopGet() {
		$this->assertEquals( array(), noop::get( '', array() ) );
		$this->assertEquals( array( 'key'=>'value' ), noop::get( '', array( 'key'=>'value' ) ) );
		$this->assertEquals( array( 'key'=>'value' ), noop::get( '/', array( 'key'=>'value' ) ) );
		$this->assertEquals( 'value', noop::get( 'key/subkey', array( 'key'=>array( 'subkey'=>'value' ) ) ) );
		
		$this->assertNull( noop::get( 123, array() ) );
		$this->assertNull( noop::get( '', 'not an array' ) );
		$this->assertNull( noop::get( 'undefined', array( 'key'=>'value' ) ) );
		$this->assertNull( noop::get( 'un/de/fi/ned', array( 'key'=>'value' ) ) );
		$this->assertNull( noop::get( 'key/undefined', array( 'key'=>'value' ) ) );
	}
	
	function testNoopSet() {
		$arr = array();
		noop::set( 'key', 'value', $arr );
		$this->assertEquals( 'value', $arr['key'] );
		
		$arr = array();
		noop::set( 'path/to/key', 'value', $arr );
		$this->assertEquals( 'value', $arr['path']['to']['key'] );
	}
	
	function testNoopDel() {
		$arr = array();
		$out = noop::del( '', $arr );
		$this->assertEquals( $out, FALSE );
		
		$arr = array();
		$out = noop::del( 'unknown/key', $arr );
		$this->assertEquals( $out, NULL );
		
		$arr = array( 'key'=>'value' );
		$out = noop::del( 'key', $arr );
		$this->assertEquals( $out, 'value' );
		$this->assertArrayNotHasKey( 'key', $arr );
		
		$arr = array();
		$arr['path']['to']['key'] = 'value';
		$out = noop::del( 'path/to/key', $arr );
		$this->assertEquals( $out, 'value' );
		$this->assertArrayNotHasKey( 'key', $arr['path']['to'] );
	}
	
	function testNoopGetWithInternalRegistry() {
		$this->assertEquals( 'index', noop::get( 'config/default/controller' ) );
	}
	
}
