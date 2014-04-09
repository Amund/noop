<?php

// 2014 - NOOP 2.0.1
// 2013 - NOOP 2.0
// 2010 - NOOP 1.0

// MIT License
// Copyright (c) 2010 Dimitri Avenel

// Permission is hereby granted, free of charge, to any person obtaining
// a copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to
// permit persons to whom the Software is furnished to do so, subject to
// the following conditions:

// The above copyright notice and this permission notice shall be
// included in all copies or substantial portions of the Software.

// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
// EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
// NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
// LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
// OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
// WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

class NoopException extends Exception {}
class NoopConfigException extends NoopException {}
class NoopControllerException extends NoopException {}
class NoopViewException extends NoopException {}

class noop {

	// All registry in an associative array
	private static $var = array(
		'config'=>array( // default config
			'default'=> array(
				'controller'=>'index',
				'lang'=>'en',
				'mime'=>'html',
			),
			'path'=> array(
				'controller'=>'secure/control',
				'view'=>'secure/view',
				//'cache'=>'secure/cache',
			),
			'mime'=>array(
				'text'=>'text/plain; charset=UTF-8',
				'html'=>'text/html; charset=UTF-8',
				'json'=>'application/json; charset=UTF-8',
			),
			//'cache'=>array(
			//	'active'=>FALSE,
			//),
			'pdo'=>array(),
			'dev'=>array(
				'debug'=>TRUE,
				'inspect'=>'<pre style="font:12px/13px Consolas,\'Lucida Console\',monospace;text-align:left;color:#ddd;background-color:#222;padding:5px;">%s</pre>'
			),
		),
		'app'=>array(), // Noop app infos
		'request'=>array(), // parsed url and controller related vars
		'benchmark'=>array(), // collection of benchmarks
		'pdo'=>array(), // pdo connections objects
		'var'=>array(), // other user vars...
	);
	
	// Merge/replace config array with $var['config']
	public static function config( $config ) {
		if( !is_array( $config ) )
			throw new NoopConfigException( 'Invalid configuration data' );
		
		//self::$var['config'] = array_merge( self::$var['config'], $config );
		self::$var['config'] = self::_array_extend( self::$var['config'], $config );
		
		// clean trailing slashes in pathes
		if( is_array( self::$var['config']['path'] ) )
			foreach( self::$var['config']['path'] as $k=>$v )
				self::$var['config']['path'][$k] = rtrim( $v, '/' );
	}
	
	// Launch Noop app
	public static function start() {
		
		// Attach error handlers
		set_error_handler( array( 'noop', '_error_handler' ) );
		set_exception_handler( array( 'noop', '_exception' ) );
		ini_set( 'display_errors', ( self::get( 'config/dev/debug' ) ? 1 : 0 ) );
		
		// Start buffering
		ob_start();
		
		// Start benchmarking
		self::benchmark('Page', TRUE);
		
		// Process request
		self::_parseApp();
		self::_parseRequest( $_SERVER['REQUEST_URI'] );
		self::_controller( self::$var['request']['url'] );
		
		
		// Include controllers
		foreach( self::$var['controllers'] as $inc )
			require_once $inc;
		unset( $inc );
		
		// Send response, if not already done
		self::output( NULL, self::$var['config']['default']['mime'], FALSE );
		
		die( 'WTF?' );
	}
	
	// Load a view file, run it, and return the string result
	public static function view( $name, $data=NULL, $cache=0 ) {
		
		$path = self::$var['config']['path']['view'].'/'.$name.'.php';
		if( !is_file($path) )
			throw new NoopViewException( 'View "'.$name.'" not found' );
		
		$cacheOn = ( !isset( $_GET['no-cache'] ) && self::get( 'config/cache/active' ) == 1 && $cache > 0 );
		$writeCache = FALSE;
		
		if( $cacheOn ) {
			$cachePath = realpath( self::$var['config']['path']['cache'].DIRECTORY_SEPARATOR.'view' );
			if( !is_dir( $cachePath ) )
				mkdir( $cachePath, TRUE );
			$file[] = $cachePath;
			$file[] = DIRECTORY_SEPARATOR;
			$file[] = substr( hash( 'md5', $name ), 0, 8 );
			$file[] = '-';
			$file[] = substr( hash( 'md5', json_encode( $data ) ), 0, 8 );
			$file[] = '.view';
			$file = implode( '', $file );
			
			$writeCache = ( !is_file( $file ) || ( time() - filemtime( $file ) >= $cache ) );
			if( !$writeCache ) {
				$content = file_get_contents( $file );
				header( 'X-File-Cache: 1' );
				return $content;
			}
		}
		
		$content = self::_view( $path, $data );
		
		if( $cacheOn && $writeCache ) {
			file_put_contents( $file, $content );
		}
		
		return $content;
	}
	
	// Return new PDO connection or use existing one
	public static function pdo( $name, $pdo_options=NULL ) {
		// existing pdo object
		if( isset( self::$var['pdo'][$name] ) )
			return self::$var['pdo'][$name];
		// new pdo object
		if( empty( self::$var['config']['pdo'][$name] ) )
			throw new NoopConfigException( 'Unknown "'.$name.'" database' );
		$param = explode( ',', self::$var['config']['pdo'][$name] );
		switch( $param[0] ) {
		case 'mysql':
			self::$var['pdo'][$name] = new PDO( 'mysql:'.$param[1], $param[2], $param[3] );
			self::$var['pdo'][$name]->query( 'SET NAMES "UTF8"' );
			break;
		case 'sqlite':
			$path = realpath( $param[1] );
			if( $path===FALSE )
				throw new NoopConfigException( '"'.$name.'" database not found' );
			self::$var['pdo'][$name] = new PDO( 'sqlite:'.$path );
			break;
		default:
			throw new NoopConfigException( 'Bad "'.$name.'" database configuration' );
		}
		return self::$var['pdo'][$name];
	}
	
	// Access $var array from a path. ex: noop::get('config/default/controller') => 'index'
	public static function get( $path, $arr=NULL ) {
		if( $arr === NULL )
			$arr = &self::$var;
		
		if( !is_string( $path ) || !is_array( $arr ) )
			return NULL;
		
		$path = trim( $path, '/' );
		if( $path == '' )
			return $arr;
		
		$current = &$arr;
		$segments = explode( '/', $path );
		foreach( $segments as $segment ) {
			if( !isset( $current[$segment] ) )
				return NULL;
			$current = &$current[$segment];
		}
		return $current;
	}
	
	// Assign a variable in $var from a path. ex: noop::set('config/default/controller','myindex')
	public static function set( $path, $value, &$arr=NULL ) {
		if( $arr === NULL )
			$arr = &self::$var;
		
		if( !is_string( $path ) || !is_array( $arr ) )
			return FALSE;
		
		$path = trim( $path, '/' );
		
		$current = &$arr;
		$segments = explode('/', $path);
		foreach( $segments as $segment ) {
			if( !isset( $current[$segment] ) )
				$current[$segment] = array();
			$current = &$current[$segment];
		}
		$current = $value;
		return TRUE;
	}
	
	// Delete a variable in $var from a key, and return value. ex: noop::del('tmp/myvar')
	public static function del( $path, &$arr=NULL ) {
		if( $arr === NULL )
			$arr = &self::$var;
		
		if( !is_string( $path ) || !is_array( $arr ) )
			return FALSE;
		
		$path = trim( $path, '/' );
		
		$current = &$arr;
		$segments = explode( '/', $path );
		foreach( $segments as $segment ) {
			if( !isset( $current[$segment] ) )
				return NULL;
			$last = &$current;
			$current = &$current[$segment];
		}
		$out = $current;
		unset( $last[$segment] );
		return $out;
	}
	
	// HTTP output
	public static function output( $content=NULL, $type=NULL ) {
		if( is_null( $content ) ) { // use the global buffer...
			$content = ob_get_clean();
		} else {// ...or start with an empty buffer
			ob_end_clean();
		}
		ob_start();
		if( preg_match( '#.+/.+#', $type ) ) {
			$mime = $type;
		} else {
			$defaultType = self::get( 'config/default/type' );
			$type = ( is_null( $type ) ? $defaultType : $type );
			$type = ( is_null( $type ) ? 'html' : $type );
			$mime = self::get( 'config/mime/'.$type );
			$mime = ( is_null( $mime ) ? 'text/plain' : $mime );
		}
		header( 'Content-Type: '.$mime );
		// end page benchmark
		self::benchmark( 'Page', FALSE ); // stop benchmark
		self::benchmark( 'Page' ); // set header
		// Send final result
		echo $content;
		header( 'Content-Length: ' . ob_get_length() );
		die();
	}
	
	// HTTP redirection
	public static function redirect( $url, $code=302 ) {
		header( 'Location: '.$url, TRUE, $code );
		die();
	}
	
	// HTTP status page
	public static function status( $code, $status, $content='', $type='html' ) {
		header( $_SERVER['SERVER_PROTOCOL'].' '.$code.' '.utf8_decode( $status ) );
		self::output( ( empty( $content ) ? $status : $content ), $type );
	}

	// HELPER Check regexp validity of a var, or an array of vars
	public static function check( $reg, $var ) {
		if( is_array( $var ) && is_array( $reg ) ) {
			$errors = array();
			foreach( $reg as $k=>$v ) {
				if( isset( $var[$k] ) ) {
					$c = self::check( $v, $var[$k] );
					if( is_array( $c ) ) {
						$errors[$k] = $c;
					} elseif( is_bool( $c ) && !$c ) {
						$errors[$k] = FALSE;
					}
				}
			}
			return $errors;
		} elseif( is_string( $var ) && is_string( $reg ) ) {
			$test = FALSE;
			//try {
				$test = preg_match( $reg, $var );
			//} catch( Exception $e ) {}
			return (bool) $test;
		} else {
			trigger_error( 'check bad arguments' );
		}
	}
	
	// HELPER Return a filtered array by deleting not allowed keys in the source array
	public static function filter( $src, $allowed=array() ) {
		return array_intersect_key( $src, array_flip( $allowed ) );
	}
	
	// DEV Print $var from a path. ex: noop::inspect( 'config/default' )
	public static function inspect( $path='', $return=FALSE, $arr=NULL ) {
		if( $arr === NULL )
			$arr = &self::$var;
		$out = sprintf( self::$var['config']['dev']['inspect'], print_r( self::get( $path, $arr ), TRUE ) );
		if( !$return ) echo $out; else return $out;
	}
	
	// DEV Benchmark ($action : TRUE=>start, FALSE=>stop, NULL=>add HTTP header)
	public static function benchmark( $name, $action=NULL ) {
		// store tics...
		if( !is_null( $action ) ) {
			if( $action ) {
				unset( self::$var['benchmark'][$name]['stop'] );
				self::$var['benchmark'][$name]['start'] = microtime( TRUE );
			} else {
				self::$var['benchmark'][$name]['stop'] = microtime( TRUE );
			}
			return TRUE;
		}
		
		// ... or return time
		$time = self::$var['benchmark'][$name]['stop'] - self::$var['benchmark'][$name]['start'];
		$time = number_format( round( $time, 6 ), 6 );
		header( 'X-Benchmark-'.ucfirst( strtolower( $name ) ).': '.$time );
		return $time;
	}
	
	// Load a view file without vars pollution
	public static function _view( $_pathToView, $data=NULL ) {
		ob_start();
		require $_pathToView;
		return ob_get_clean();
	}
	
	// Parse request and populate app var
	public static function _parseApp() {
		$protocol  = (
			isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on'
			? 'https://'
			: 'http://'
		);
		$host = $_SERVER['HTTP_HOST'];
		$port = (
			isset( $_SERVER['SERVER_PORT'] ) && in_array( $_SERVER['SERVER_PORT'], array( 80, 443 ) )
			? ''
			: ':'.$_SERVER['SERVER_PORT'] );
		$dir = dirname( $_SERVER['SCRIPT_NAME'] );
		$dir = ( $dir=='/' ? '' : $dir );
		
		self::set( 'app/protocol', $protocol );
		self::set( 'app/host', $host );
		self::set( 'app/port', $port );
		self::set( 'app/dir', $dir );
		self::set( 'app/url', $protocol.$host.$port.$dir );
		self::set( 'app/path', dirname( __FILE__ ) );
	}
	
	// Parse request and populate request var
	public static function _parseRequest( $request_uri, $dir=NULL ) {
		if( $dir === NULL )
			$dir = self::$var['app']['dir'];
		
		$url = preg_replace( '/\\?.*/', '', $request_uri );
		$url = substr( $url, strlen( $dir ) );
		if( $url !== '/' )
			$url = rtrim( $url, '/' );
		$qs = (
			strpos( $request_uri, '?' ) !== FALSE
			? preg_replace( '/.*\\?/', '', $request_uri )
			: ''
		);
		
		$canonical = $url;
		if( $qs !== '' )
			$canonical .= '?'.$qs;
		
		//if( $request_uri != self::get( 'app/dir' ).$canonical )
		//	self::redirect( self::get( 'app/url' ).$canonical );
		//$canonical = self::get( 'app/url' ).$canonical;
		
		$ajax = ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' );
		
		$method = $_SERVER['REQUEST_METHOD'];
		if( !empty( $_REQUEST['_method'] ) )
			$method = $_REQUEST['_method'];
		
		self::set( 'request/url', $url );
		self::set( 'request/qs', $qs );
		self::set( 'request/canonical', $canonical );
		self::set( 'request/canonical-url', self::$var['app']['url'].$canonical );
		self::set( 'request/ajax', $ajax );
		self::set( 'request/method', $method );
	}
	
	// Parse "controller" (request url) and prepare includes list
	public static function _controller( $url ) {
		$config_default_controller = self::$var['config']['default']['controller'];
		$config_path_controller    = rtrim( self::$var['config']['path']['controller'], '/' );
		
		$segments = trim( urldecode( $url ), '/' );
		$segments = ( $segments === '' ? $config_default_controller : $segments );
		if( $segments === '' || !is_dir( $config_path_controller ) )
			throw new NoopConfigException( 'Controller path not found' );
		
		$segments = explode( '/', $segments );
		self::$var['controllers'] = array();
		$controller = '';
		$lastfile = '';
		$trail = '';
		
		foreach( $segments as $key=>$segment ) {
			$controller .= '/'.$segment;
			$dir = $config_path_controller.$controller;
			$file = $dir . '.php';
			$isdir = is_dir( $dir );
			$isfile = is_file( $file );
			if( $isdir || $isfile ) unset( $segments[$key] );
			if( $isfile ) {
				array_push( self::$var['controllers'], $file );
				$lastfile = $controller;
				$trail = '';
			} else {
				$trail .= '/'.$segment;
			}
		}
		$controller = $lastfile;
		
		$file = $dir . '/' . $config_default_controller . '.php';
		if( is_file( $file ) ) {
			array_push( self::$var['controllers'], $file );
			//$controller = $dir;
		}
		
		self::set( 'request/controller', $controller );

		self::set( 'request/controller-url', self::$var['app']['url'].$controller );
		self::set( 'request/trail', trim( $trail, '/' ) );
		
		if( count( self::$var['controllers'] ) == 0 )
			throw new NoopControllerException( 'Controller "'.self::$var['request']['controller'].'" not found', 404 );
	}
	
	// Error handling
	public static function _error_handler( $errno, $errstr, $errfile, $errline ) {
		throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
	}
	
	// Exception handling
	public static function _exception( $exception ) {
		$data = array();
		$data['type'] = get_class( $exception );
		$data['code'] = $exception->getCode();
		$data['message'] = $exception->getMessage();
		$data['file'] = $exception->getFile();
		$data['line'] = $exception->getLine();
		
		if( $data['code'] == 0 )
			$data['code'] = 500;
		
		$output = '';
		
		if( self::get( 'config/dev/debug' ) === TRUE ) {
		
			$i = 0;
			$data['trace'] = array();
			foreach ($exception->getTrace() as $k=>$v) {
				$data['trace'][$k][] = '#'.$k.' ';
				$data['trace'][$k][] = ( isset( $v['file'] ) ? $v['file'] : '' );
				$data['trace'][$k][] = ( isset( $v['line'] ) ? '('.$v['line'].') ' : '' );
				$data['trace'][$k][] = ( isset( $v['function'] ) ? $v['function'] : '' );
				$data['trace'][$k][] = ( isset( $v['args'] ) ? '('.implode(", ", array_map( function ($e) { return var_export($e, true); }, $v["args"] ) ).')' : '' );
				$data['trace'][$k] = implode( '', $data['trace'][$k] );
			}
			$data['trace'] = implode( "\n", $data['trace'] );
			
			$view = self::get( 'config/error/view' );
			if( !empty( $view ) ) {
				$output = self::view( $view, $data, TRUE );
			} else {
				$output = sprintf(
					"<pre><h1>Error %s</h1><b>Type</b>    : %s\n<b>Message</b> : %s\n<b>File</b>    : %s(%s)\n\n<b>Stack trace :</b>\n%s\n</pre>",
					$data['code'], $data['type'], $data['message'], $data['file'], $data['line'], $data['trace']
				);
			}
			
		}
		
		noop::status( $data['code'], $data['message'], $output, 'html' );
	}

	// "Extend" array $a by array $b
	public static function _array_extend( $a, $b ) {
		foreach( $b as $k=>$v ) {
			if( is_array( $v ) ) {
				if( !isset( $a[$k] ) ) {
					$a[$k] = $v;
				} else {
					$a[$k] = self::_array_extend( $a[$k], $v );
				}
			} else {
				$a[$k] = $v;
			}
		}
		return $a;
	}

}
