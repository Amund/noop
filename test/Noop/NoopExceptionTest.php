<?php

class NoopExceptionTest extends PHPUnit_Framework_TestCase {
	
	function tearDown() {
		noop::set( 'config/path/controller', 'app/control' );
		noop::set( 'config/default/controller', 'index' );
	}
	
    /**
     * @expectedException NoopConfigException
     */
	function testNoopControllerNotExistingPath() {
		noop::set( 'config/path/controller', __DIR__.'/non-existing-path' );
		noop::set( 'config/default/controller', 'existing-controller' );
		try {
			noop::_controller( '' );
		} catch( InvalidArgumentException $e ) {
			return;
		}
		$this->fail();
	}
	
    /**
     * @expectedException NoopConfigException
     */
	function testNoopControllerEmptyDefault() {
		noop::set( 'config/path/controller', __DIR__ );
		noop::set( 'config/default/controller', '' );
		try {
			noop::_controller( '' );
		} catch( InvalidArgumentException $expected ) {
			return;
		}
		$this->fail();
	}
	
    /**
     * @expectedException NoopControllerException
     */
	function testNoopControllerNotExists() {
//		print_r( noop::get( 'app' ) );
		noop::set( 'config/path/controller', __DIR__ );
		noop::set( 'config/default/controller', 'default' );
		try {
			noop::_controller( 'sub-without-default' );
		} catch( InvalidArgumentException $expected ) {
			return;
		}
		$this->fail();
	}
	
}
