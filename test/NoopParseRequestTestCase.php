<?php

ini_set( 'display_errors', 1 );
require_once __DIR__.'/../noop.php';

class NoopParseRequestTest extends PHPUnit_Framework_TestCase {
	
	function testNoopParseRequest() {
		$dir = '/path/to/noop';
		
		noop::_parseRequest( '/path/to/noop/', $dir );
		$this->assertEquals( '/', noop::get( 'request/url' ) );
		
		noop::_parseRequest( '/path/to/noop/folder', $dir );
		$this->assertEquals( '/folder', noop::get( 'request/url' ) );
		
		noop::_parseRequest( '/path/to/noop/folder/', $dir );
		$this->assertEquals( '/folder', noop::get( 'request/url' ) );
		
		noop::_parseRequest( '/path/to/noop/folder1/folder2', $dir );
		$this->assertEquals( '/folder1/folder2', noop::get( 'request/url' ) );
		
		noop::_parseRequest( '/path/to/noop/folder1/folder2/', $dir );
		$this->assertEquals( '/folder1/folder2', noop::get( 'request/url' ) );
		
		noop::_parseRequest( '/path/to/noop/folder_é', $dir );
		$this->assertEquals( '/folder_é', noop::get( 'request/url' ) );
	}
	
	function testNoopParseRequestWithQuerystring() {
		$dir = '/path/to/noop';
		
		noop::_parseRequest( '/path/to/noop/?', $dir );
		$this->assertEquals( '/', noop::get( 'request/url' ) );
		$this->assertEquals( '', noop::get( 'request/qs' ) );
		
		noop::_parseRequest( '/path/to/noop/?qs', $dir );
		$this->assertEquals( '/', noop::get( 'request/url' ) );
		$this->assertEquals( 'qs', noop::get( 'request/qs' ) );
		
		noop::_parseRequest( '/path/to/noop/folder?', $dir );
		$this->assertEquals( '/folder', noop::get( 'request/url' ) );
		$this->assertEquals( '', noop::get( 'request/qs' ) );
		
		noop::_parseRequest( '/path/to/noop/folder/?qs', $dir );
		$this->assertEquals( '/folder', noop::get( 'request/url' ) );
		$this->assertEquals( 'qs', noop::get( 'request/qs' ) );
	}
	
}
