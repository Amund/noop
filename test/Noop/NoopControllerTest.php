<?php

ini_set( 'display_errors', 1 );
require_once __DIR__.'/../noop.php';

class NoopControllerTest extends PHPUnit_Framework_TestCase {
	
	function tearDown() {
		noop::set( 'config/path/controller', 'secure/control/' );
	}
	
	
	function testNoopController01() {
		$path =  __DIR__.'/fixtures/test01';
		noop::set( 'config/path/controller', $path );
		
		//noop::_controller( '' );
		//noop::_controller( 'trail' );
		
	}
	
	function testNoopController02() {
		$path =  __DIR__.'/fixtures/test02';
		noop::set( 'config/path/controller', $path );
		
		//noop::_controller( '' );
		//noop::_controller( 'trail' );
		
		noop::_controller( 'controller' );
		$this->assertEquals( noop::get( 'request/controller' ), '/controller' );
		$this->assertEquals( noop::get( 'request/trail' ), '' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/controller.php'
		) );
		
		noop::_controller( 'controller/trail' );
		$this->assertEquals( noop::get( 'request/controller' ), '/controller' );
		$this->assertEquals( noop::get( 'request/trail' ), 'trail' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/controller.php'
		) );
	}
	
	function testNoopController03() {
		$path =  __DIR__.'/fixtures/test03';
		noop::set( 'config/path/controller', $path );
		
		noop::_controller( '' );
		$this->assertEquals( noop::get( 'request/controller' ), '/index' );
		$this->assertEquals( noop::get( 'request/trail' ), '' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/index.php'
		) );
		
		//noop::_controller( 'trail' );
		
		noop::_controller( 'index/trail' );
		$this->assertEquals( noop::get( 'request/controller' ), '/index' );
		$this->assertEquals( noop::get( 'request/trail' ), 'trail' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/index.php'
		) );
	}
	
	function testNoopController04() {
		$path =  __DIR__.'/fixtures/test04';
		noop::set( 'config/path/controller', $path );
		
		//noop::_controller( '' );
		//noop::_controller( 'trail' );
		//noop::_controller( 'sub' );
	}
	
	function testNoopController05() {
		$path =  __DIR__.'/fixtures/test05';
		noop::set( 'config/path/controller', $path );
		
		noop::_controller( '' );
		$this->assertEquals( noop::get( 'request/controller' ), '/index' );
		$this->assertEquals( noop::get( 'request/trail' ), '' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/index.php'
		) );
		
		noop::_controller( 'controller' );
		$this->assertEquals( noop::get( 'request/controller' ), '/controller' );
		$this->assertEquals( noop::get( 'request/trail' ), '' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/controller.php'
		) );
	}
	
	function testNoopController06() {
		$path =  __DIR__.'/fixtures/test06';
		noop::set( 'config/path/controller', $path );
		
		noop::_controller( 'controller' );
		$this->assertEquals( noop::get( 'request/controller' ), '/controller' );
		$this->assertEquals( noop::get( 'request/trail' ), '' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/controller.php'
		) );
		
		noop::_controller( 'controller/sub' );
		$this->assertEquals( noop::get( 'request/controller' ), '/controller' );
		$this->assertEquals( noop::get( 'request/trail' ), 'sub' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/controller.php'
		) );
		
		noop::_controller( 'controller/trail' );
		$this->assertEquals( noop::get( 'request/controller' ), '/controller' );
		$this->assertEquals( noop::get( 'request/trail' ), 'trail' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/controller.php'
		) );
		
		//noop::_controller( '' );
		//noop::_controller( 'sub' );
	}
	
	function testNoopController07() {
		$path =  __DIR__.'/fixtures/test07';
		noop::set( 'config/path/controller', $path );
		
		noop::_controller( '' );
		$this->assertEquals( noop::get( 'request/controller' ), '/index' );
		$this->assertEquals( noop::get( 'request/trail' ), '' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/index.php'
		) );
		
		//noop::_controller( 'trail' );
		//noop::_controller( 'sub' );
	}
	
	function testNoopController08() {
		$path =  __DIR__.'/fixtures/test08';
		noop::set( 'config/path/controller', $path );
		
		noop::_controller( 'sub/controller' );
		$this->assertEquals( noop::get( 'request/controller' ), '/sub/controller' );
		$this->assertEquals( noop::get( 'request/trail' ), '' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/sub/controller.php'
		) );
		
		noop::_controller( 'sub/controller/trail' );
		$this->assertEquals( noop::get( 'request/controller' ), '/sub/controller' );
		$this->assertEquals( noop::get( 'request/trail' ), 'trail' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/sub/controller.php'
		) );
		
		//noop::_controller( 'sub' );
		//noop::_controller( 'sub/trail' );
	}
	
	function testNoopController09() {
		$path =  __DIR__.'/fixtures/test09';
		noop::set( 'config/path/controller', $path );
		
		noop::_controller( 'sub/sub/controller' );
		$this->assertEquals( noop::get( 'request/controller' ), '/sub/sub/controller' );
		$this->assertEquals( noop::get( 'request/trail' ), '' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/sub/sub/controller.php'
		) );
		
		noop::_controller( 'sub/sub/controller/trail' );
		$this->assertEquals( noop::get( 'request/controller' ), '/sub/sub/controller' );
		$this->assertEquals( noop::get( 'request/trail' ), 'trail' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/sub/sub/controller.php'
		) );
		
		//noop::_controller( 'sub' );
		//noop::_controller( 'sub/trail' );
		//noop::_controller( 'sub/sub' );
		//noop::_controller( 'sub/sub/trail' );
	}
	
	function testNoopController10() {
		$path =  __DIR__.'/fixtures/test10';
		noop::set( 'config/path/controller', $path );
		
		noop::_controller( 'sub/sub/controller' );
		$this->assertEquals( noop::get( 'request/controller' ), '/sub/sub/controller' );
		$this->assertEquals( noop::get( 'request/trail' ), '' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/sub.php',
			$path.'/sub/sub/controller.php'
		) );
		
		noop::_controller( 'sub/sub/controller/trail' );
		$this->assertEquals( noop::get( 'request/controller' ), '/sub/sub/controller' );
		$this->assertEquals( noop::get( 'request/trail' ), 'trail' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/sub.php',
			$path.'/sub/sub/controller.php'
		) );
		
		noop::_controller( 'sub' );
		$this->assertEquals( noop::get( 'request/controller' ), '/sub' );
		$this->assertEquals( noop::get( 'request/trail' ), '' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/sub.php'
		) );
		
		noop::_controller( 'sub/trail' );
		$this->assertEquals( noop::get( 'request/controller' ), '/sub' );
		$this->assertEquals( noop::get( 'request/trail' ), 'trail' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/sub.php'
		) );
		
		noop::_controller( 'sub/1/2' );
		$this->assertEquals( noop::get( 'request/controller' ), '/sub' );
		$this->assertEquals( noop::get( 'request/trail' ), '1/2' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/sub.php'
		) );
		
		noop::_controller( 'sub/sub' );
		$this->assertEquals( noop::get( 'request/controller' ), '/sub' );
		$this->assertEquals( noop::get( 'request/trail' ), 'sub' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/sub.php'
		) );
		
		//noop::_controller( 'sub/sub' );
		//noop::_controller( 'sub/sub/trail' );
	}
	
	function testNoopController11() {
		$path =  __DIR__.'/fixtures/test11';
		noop::set( 'config/path/controller', $path );
		
		noop::_controller( 'sub/sub/controller' );
		$this->assertEquals( noop::get( 'request/controller' ), '/sub/sub/controller' );
		$this->assertEquals( noop::get( 'request/trail' ), '' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/sub/sub.php',
			$path.'/sub/sub/controller.php'
		) );
		
		noop::_controller( 'sub/sub/controller/trail' );
		$this->assertEquals( noop::get( 'request/controller' ), '/sub/sub/controller' );
		$this->assertEquals( noop::get( 'request/trail' ), 'trail' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/sub/sub.php',
			$path.'/sub/sub/controller.php'
		) );
		
		noop::_controller( 'sub/sub' );
		$this->assertEquals( noop::get( 'request/controller' ), '/sub/sub' );
		$this->assertEquals( noop::get( 'request/trail' ), '' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/sub/sub.php'
		) );
		
		noop::_controller( 'sub/sub/trail' );
		$this->assertEquals( noop::get( 'request/controller' ), '/sub/sub' );
		$this->assertEquals( noop::get( 'request/trail' ), 'trail' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/sub/sub.php'
		) );
		
		noop::_controller( 'sub/sub/1/2' );
		$this->assertEquals( noop::get( 'request/controller' ), '/sub/sub' );
		$this->assertEquals( noop::get( 'request/trail' ), '1/2' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/sub/sub.php'
		) );
		
		//noop::_controller( 'sub' );
		//noop::_controller( 'sub/trail' );
	}
	
	function testNoopController12() {
		$path =  __DIR__.'/fixtures/test12';
		noop::set( 'config/path/controller', $path );
		
		noop::_controller( 'sub/sub/controller' );
		$this->assertEquals( noop::get( 'request/controller' ), '/sub/sub/controller' );
		$this->assertEquals( noop::get( 'request/trail' ), '' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/sub.php',
			$path.'/sub/sub.php',
			$path.'/sub/sub/controller.php'
		) );
		
		noop::_controller( 'sub/sub/controller/trail' );
		$this->assertEquals( noop::get( 'request/controller' ), '/sub/sub/controller' );
		$this->assertEquals( noop::get( 'request/trail' ), 'trail' );
		$this->assertEquals( noop::get( 'request/includes' ), array(
			$path.'/sub.php',
			$path.'/sub/sub.php',
			$path.'/sub/sub/controller.php'
		) );
	}
	
}
