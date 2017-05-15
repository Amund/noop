<?php

class NoopFilterTest extends PHPUnit_Framework_TestCase {
	
	function testFilter01() {
		$src = array(
			'a'=>'1',
			'b'=>'2',
			'c'=>'  value 3  ',
		);
		$allowed = array( 'a','c' );
		$expected = array(
			'a'=>'1',
			'c'=>'value 3',
		);
		$this->assertEquals( noop::filter( $src, $allowed ), $expected );
	}
	
	/*function testFilter02() {
		$reg = '#^\d+$#';
		$var = 'error';
		$this->assertEquals( FALSE, noop::check( $reg, $var ) );
	}
	
	function testCheck03() {
		$reg = array(
			'key1'=>'#^\w{4}$#',
			'key2'=>'#^\d+$#',
		);
		$var = array(
			'key1'=>'test',
			'key2'=>'1234',
		);
		$errors = noop::check( $reg, $var );
		$this->assertEquals( array(), noop::check( $reg, $var ) );
	}
	
	function testCheck04() {
		$reg = array(
			'key1'=>'#^\w{4}$#',
			'key2'=>'#^\d+$#',
		);
		$var = array(
			'key1'=>'test',
			'key2'=>'error',
		);
		$errors = noop::check( $reg, $var );
		$this->assertEquals( array( 'key2'=>FALSE ), noop::check( $reg, $var ) );
	}*/
	
}
