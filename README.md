# noop

`noop` is a *really* tiny PHP framework for PHP project, based on a *really* free [MVC architecture](#mvc). The main idea behind this library is being unopinionated, and therefore staying as close as possible of... PHP. There are no PHP extensions in this library, it's just about organization. You know PHP, you'll use PHP.

**Download** : https://github.com/Amund/Noop

**Installation** : Unzip, and open the "public" folder in your browser to start coding, or, better, use a [virtualhost](https://httpd.apache.org/docs/2.4/vhosts/examples.html) to expose only this folder. Note that you must activate `mod_rewrite` module on your Apache server.

Technically, `noop` is a **one file** library ("*noop.php*"), and a **one object** library ("*noop*"). [All methods](#api) in this object are *statics*, and can be classified this way :

Type|Methods
----:|:----
Core | [`config`](#method-config), [`start`](#method-start), [`view`](#method-view) and [`pdo`](#method-pdo)
Registry / Arrays | [`get`](#method-get), [`set`](#method-set), [`del`](#method-del)
HTTP response | [`output`](#method-output), [`redirect`](#method-redirect), [`status`](#method-status)
Helpers | [`check`](#method-check), [`filter`](#method-filter)
Dev tools | [`inspect`](#method-inspect), [`benchmark`](#method-benchmark)

<a name="registry"></a>
## Registry system [^](#noop)
All datas in `noop` are stored in a unique multilevel associative array, its registry system. It's globally accessible without polluting global scope, and organized as follow :
- `config` All configuration variables in there
- `app` App related infos, calculated from a request
- `request` Details of the request
- `controllers` Collection of PHP scripts to include, calculated from a request
- `pdo` Collection of [PDO](http://php.net/manual/book.pdo.php) instances already created, used as a cache system
- `benchmark` Collection of benchmarks
- `var` Your playground, store anything you want...

During your development, you can inspect this registry at any time with the method [`inspect`](#method-inspect).
    
```php
// Inspect all the registry...
echo noop::inspect();

// ...or a part...
echo noop::inspect( 'config' );

// ...or a part of a part...
echo noop::inspect( 'config/path' );

// ...or... you got it.
echo noop::inspect( 'config/path/controller' );
```

You can also manage all these keys with the [`get`](#method-get), [`set`](#method-set) and [`del`](#method-del) methods.
	
```php
// Store a new key...
noop::set( 'var/foo', 'bar' );

// ...display it...
echo noop::get( 'var/foo' );

// ...and remove it
noop::del( 'var/foo' );
```

Because it is a simple associative array, you can do any manipulation you want in the registry. But all the root's keys (except `var`) are internally managed, so use them with caution.



<a name="mvc"></a>
## MVC architecture [^](#noop)

### Models [^](#noop)

There is no direct support for anything related to models in `noop`. PHP objects are powerful enough !

However, to stay in a `noop` way, you still have a special location where to store your classes and component, in the folder `app/model`. A [PSR-4](http://www.php-fig.org/psr/psr-4/) classes autoloader is included, and registered when app start, which check that folder by default. You can modify that default path in the `config.php` file, check noop source to override it.

If you already have an autoload, you can register it before `noop::start()` in `public/index.php`, and disable the internal one with a config path equal to `FALSE`. You can also include the [composer](https://getcomposer.org/)'s [one](https://getcomposer.org/doc/01-basic-usage.md#autoloading), if you use it.

### Controllers [^](#noop)

A controller in `noop` is a PHP script file, or a collection of script files, located in the controller folder. By default, this folder is `app/control`, and can be modified in the `config.php`, by setting a new path for the key `path/controller`. There is also a default controller, `index`, that can be overriden the same way, for the key `default/controller`. I'll explain its usage next.

```php
noop::config( array(
	// ...
	'default'=> array(
		'controller'=> 'homepage'
		// ...
	),
	'path'=> array(
		'controller'=> 'my/new/path'
		// ...
	)
	// ...
) );
```

Or, since PHP 5.4, with the short syntax.

```php
noop::config( [
	// ...
	'default'=> [ 'controller'=> 'homepage' ],
	'path'=> [ 'controller'=> 'my/new/path' ]
	// ...
] );
```

Then, here's the fun part.

The controller is called by an URL, as many other MVC framework. But no routing table around, at least not as usual : the filesystem is the routing table.


Consider this controllers folder :

```
/[noop-path]
	/app
		/control
			/product
				acme.php
			index.php
			product.php
```

Then, copy/paste this code in `index.php` and `product.php`.

```php
echo noop::inspect( 'request' ); // details on how the URL is parsed
echo noop::inspect( 'controllers' ); // What files will be included
```

Finally, take a breath, and try some URL.

I assume you've configured your web server to expose the `public` folder to localhost.

- `http://localhost/`<br>
There is no controller, this will use the default controller, `index`. So, only the file `index.php` will be included.
- `http://localhost/contact`<br>
There is no `contact.php` in the controller folder, you'll face a 404 error.
- `http://localhost/product`<br>
There is a `product.php` file, it will be included.
- `http://localhost/product/anvil`<br>
`product.php` is always a file, it will be included. `product/anvil.php` is not, so `anvil` is not a real controller, it will be considered as a trail (a parameter) for the controller `product`.
- `http://localhost/product/acme`<br>
`product.php` is included, and `acme.php` too.
- `http://localhost/product/acme/anvil`<br>
`product.php` and `acme.php` are included, and anvil become a trail.
- `http://localhost/product/acme/anvil/black/iron`<br>
Same as above, but with a long trail. To use this list of parameters in your controller, just [`explode`](http://php.net/explode) it.


```php
$trails = explode( '/', noop::get( 'request/trail' ) );
// Array (
//     [0] => anvil
//     [1] => black
//     [2] => iron
// )
```


If you've got this, you've done the hardest part of `noop`.

### Views [^](#noop)

There are 2 ways to use controllers/views. The first is a more traditional PHP way, more readable for `noop` beginners, but more verbose. The second is the pure `noop` way, for real power users.

Let's start gently.

#### The rusty PHP way

We want a page to display a product detail, say... hmm... an anvil. From acme. The URL will be

`http://localhost/product?id=123`

Consider the following controller `app/controller/product.php`. I let you implement the class `Product`, in a file `app/model/Product.php` [for example](#models-noop-).

```php
// We get the product id from the request
$id = $_GET['id'];

// In a real app, you'll then create a product instance
// $product = new Product();

// Load the product with id 123 from database
// $product->load( $id );

// Get all product properties as array
// $product_data = $product->data();

// to finally obtain :
$product_data = array( 'id'=>123, 'name'=>'Anvil', 'matter'=>'Iron', 'color'=>'Black' )

// We load the "product" view,
// inject product data in it,
// and store the returned string
$product_view = noop::view( 'product', $product_data );

// Prepare the final page
$data = array();
$data['title'] = 'Product details';
$data['content'] = $product_view;

// We get the "page" view,
// inject $data in it (with the product view),
// and echo it, this time
echo noop::view( 'page', $data );

// Then return output to browser.
noop::output( NULL, 'html' );
```
	
You'll need a product view `app/view/product.php`, using the $data variable transmitted:

```php
<div class="product">
	<label>ID</label> <?=$data['id']?><br>
	<label>Name</label> <?=$data['name']?><br>
	<label>Matter</label> <?=$data['matter']?><br>
	<label>Color</label> <?=$data['color']?><br>
</div>
```

To avoid warnings on non-existent keys in $data, we can also use `noop::get` on `$data`, as a filter:

```php
<div class="product">
	<label>ID</label> <?=noop::get( 'id', $data )?><br>
	<label>Name</label> <?=noop::get( 'name', $data )?><br>
	<label>Matter</label> <?=noop::get( 'matter', $data )?><br>
	<label>Color</label> <?=noop::get( 'color', $data )?><br>
</div>
```

And we also need a reusable standard page view `app/view/page.php`:

```php
<!DOCTYPE html>
<html>
<head>
	<title><?=noop::get( 'title', $data )?></title>
</head>
<body>

	<h1><?=noop::get( 'title', $data )?></h1>
	
	<?=noop::get( 'content', $data )?>
	
</body>
</html>
```

With all those files, you can now check `http://localhost/product?id=123` in your browser.


#### The shiny `noop` way


`noop` has a killer feature : its registry ! You know, this fabulous... simple associative array.

In this array, there is a special place you are encouraged to store in, it's `var`. It was [previously](#registry-system-noop-) presented as your playground, so let's use it, and save some code. Don't hesitate to add some `noop::inspect( 'var' )` to see its content.

And as we rewrite, we will also use the controller trail instead of querystring. URL will become `http://localhost/product/123`

	
```php
// Load product
$id = noop::get( 'request/trail' );

// Same as previously, we load product and get properties
// $product = new Product();
// $product->load( $id );
// $product_data = $product->data();
// to obtain :
$product_data = array( 'id'=>123, 'name'=>'Anvil', 'matter'=>'Iron', 'color'=>'Black' )
// But this time, we store them in the `noop` registry
noop::set( 'var/product', $product_data );

// Store the compiled product view in the `noop` registry
noop::set( 'var/product/view', noop::view( 'product' ) );

// Set the page title
noop::set( 'var/title', 'Product details' );

// Then echo page
echo noop::view( 'page' );
```
	
	
We modify the product view, to get data directly from the registry:

```php
<div class="product">
	<label>ID</label> <?=noop::get( 'var/product/id' )?><br>
	<label>Type</label> <?=noop::get( 'var/product/type' )?><br>
	<label>Name</label> <?=noop::get( 'var/product/name' )?><br>
	<label>Matter</label> <?=noop::get( 'var/product/matter' )?><br>
	<label>Color</label> <?=noop::get( 'var/product/color' )?><br>
</div>
```

And the page view too:

```php
<!DOCTYPE html>
<html>
<head>
	<title><?=noop::get( 'var/title' )?></title>
</head>
<body>

	<h1><?=noop::get( 'var/title' )?></h1>
	
	<?=noop::get( 'var/product/view' )?>
	
</body>
</html>
```

#### Choose your path
Obviously, there is no best way, it's just a matter of taste. You can choose a way, or mix them, or whatever. That's what I call unopinionated.


<a name="api"></a>
## API [^](#noop)



<a name="method-benchmark"></a>
### benchmark( `$name`[, `$action`] ) [^](#noop)

Set/Get some benchmarks, to trap too long functions, etc... during the development. A benchmark with name "page" is added internally. All benchmarks are also added as HTTP headers ("`X-Benchmark-Page: 0.123456`").

###### Parameters
- `$name` Required, String. Name of the benchmark.
- `$action` Optional, Boolean or NULL, default to NULL. TRUE start the benchmark, FALSE stop it, and NULL (or no value) return the timer.

###### Return
- If `$action` is NULL, return the benchmark value, in seconds.

###### Example
```php
noop::benchmark( 'mylongloop', TRUE ); // start
//...some code to evaluate...
noop::benchmark( 'mylongloop', FALSE ); // stop

// ...then print
echo noop::benchmark( 'mylongloop' ); // '0.123456', in seconds

// Additionally, you'll find a "X-Benchmark-mylongloop: 0.123456" in HTTP response headers
```



<a name="method-check"></a>
### check( `$reg`, `$var` ) [^](#noop)

Check if $var match the regexp $reg.

###### Parameters
- `$reg` Required, String or Array
- `$var` Required, String or Array

###### Return
- Boolean, if `$reg` and `$var` are strings
- Array of errors, if `$reg` and `$var` are arrays

###### Example
Simple version, with strings

```php
$test = noop::check( '#\d+#', '123' );
var_dump( $test ); // => TRUE

$test = noop::check( '#\d+#', 'my test' );
var_dump( $test ); // => FALSE
```

Less simple version, with arrays

```php
$reg = array( 'a'=>'#\d+#', 'b'=>'#\w{1,}#' );
$var = array( 'a'=>'123', 'b'=>'is checked', 'c'=>'is not checked' )
$errors = noop::check( $reg, $var );
var_dump( $test ); // => array()

$reg = array( 'a'=>'#\d+#', 'b'=>'#\w{1,}#' );
$var = array( 'a'=>'wrong', 'b'=>'right', 'c'=>'is not checked' )
$errors = noop::check( $reg, $var );
var_dump( $test ); // => array( 'a'=>FALSE )
```


	
<a name="method-config"></a>
### config( `$config` ) [^](#noop)

Extend/Override `noop` configuration registry. You can load different parts of a heavy configuration, configure pathes, manage DB connections, add mime types, switch configuration (dev/prod), add your own app specific configuration variables, etc...

###### Parameters
- `$config` Required, Array

###### Example
```php
var_dump( noop::get( 'config' ) );

// Array (
//     [default] => Array (
//         [controller] => index
//         [lang] => en
//         [mime] => html
//     )
// ...

$config = array(
	'default'=>array(
		'controller'=>'default',
		'lang'=>'fr'
	)
);
noop::config( $config );

var_dump( noop::get( 'config' ) );

// Array (
//     [default] => Array (
//         [controller] => default
//         [lang] => fr
//         [mime] => html
//     )
// ...
```



<a name="method-del"></a>
### del( `$path`[, `$array`] ) [^](#noop)
Delete a key in an array, based on a virtual `path` to this key.

###### Parameters
- `$path` Required, String.
- `$array` Optional, Array. If omitted, the `noop` registry is used.

###### Return
- FALSE if `$path` was empty
- NULL if the key was not found in array
- Otherwise, the deleted value is returned

###### Example
```php
$myarr = array(
	'path'=>array(
		'to'=>array(
			'key'=>'value'
		)
	)
);

noop::del( 'path/to/key', $myarr );

// Array (
//     [path] => Array (
//         [to] => Array
//             (
//             )
//     )
// )
```


<a name="method-filter"></a>
### filter( `$src`, `$allowed` ) [^](#noop)
Perform a filter on an array with a whitelist of keys. Other keys are removed.

###### Parameters
- `$src` Required, Array. The source array
- `$allowed` Required, Array. The whitelist of keys

###### Return
- A new array, with only withelisted keys

###### Example
```php
// http://test/?q=42&lang=fr&out=txt&referer=
$request = noop::filter( $_GET, array( 'q', 'lang' ) );

// Array (
//     [q] => 42
//     [lang] => fr
// )
```



<a name="method-get"></a>
### get( `$path`[, `$array`] ) [^](#noop)
Get a value from an array, based on a virtual `$path` to its key.

###### Parameters
- `$path` Required, String.
- `$array` Optional, Array. If omitted, the `noop` registry is used.

###### Return
- `$array`, if the path is empty (`''`) or root (`'/'`)
- The key value, if the `$path` is found
- Otherwise, `NULL`

###### Example
```php
$myarr = array(
	'path'=>array(
		'to'=>array(
			'key'=>'value'
		)
	)
);

echo noop::get( 'path/to/key', $myarr ); // => 'value'
```



<a name="method-inspect"></a>
### inspect( [`$path`[, `$arr`]] ) [^](#noop)
Development tool to inspect variable in a readable way.

###### Parameters
- `$path` Optional, String. The "virtual" path to the key. Default to `''`
- `$arr` Optional, Array. If omitted, the `noop` registry is used.

###### Return
- Print formatted string representation of the variable if `$return` is TRUE

###### Example
```php
// To have a look on current request
echo noop::inspect( 'request' );
```


<a name="method-output"></a>
### output( [`$content`[, `$type`]] ) [^](#noop)
Stop the current script, and send the HTTP response to the client.

###### Parameters
- `$content` Optional, String. The response body to send
- `$type` Optional, String. A valid MIME type, or a shortcut (`'text'`, `'html'` or `'json'`)



<a name="method-pdo"></a>
### pdo( `$name` ) [^](#noop)
Create a named PDO object, based on its configuration. The object is cached at the first call, for further use.

In the `config` registry, there is a dedicated `pdo` array to store your collection of connection strings. These string are constructed in the form `'[driver],[dsn],[user],[password]'`

Example: `'mysql,host=localhost;dbname=db,admin,fZ5GdsV4'`

###### Parameters
- `$name` Required, String

###### Return
- [PDO instance](http://php.net/pdo)

###### Example
Setup databases connections

```php
noop::config( array(
	'pdo'=>array(
		'db1'=>'mysql,host=localhost;dbname=db1,user1,password1',
		'db2'=>'mysql,host=localhost;dbname=db2,user2,password2',
	)
) );
```
Then use them anywhere
```php
$stmt = noop::pdo( 'db1' )->query( 'SELECT * FROM table' );
```

<a name="method-redirect"></a>
### redirect( `$url`[, `$code`] ) [^](#noop)
Stop the current script, and make an HTTP redirect to `url`.

###### Parameters
- `$url` Required, String. The URL to redirect to
- `$code` Optional, Integer. The HTTP redirect code. Default to `'302'`

###### Example
```php
// An external URL
noop::redirect( 'http://www.google.com/' );

// or a relative URL
noop::redirect( noop::get( 'app/url' ).'/contact' );
```

<a name="method-set"></a>
### set( `$path`, `$value`[, `$array`] ) [^](#noop)
Set a value from an array, based on a virtual `$path` to its key.

###### Parameters
- `$path` Required, String.
- `$value` Required, Mixed.
- `$array` Optional, Array. If omitted, the `noop` registry is used.

###### Return
- `TRUE`

###### Example
```php
$arr = array();
noop::set( 'first', 'foo', $arr );
noop::set( 'second/key', array( 1, 2, 3 ), $arr );

// Array (
//     [first] => foo
//     [second] => Array (
//         [key] => Array (
//             [0] => 1
//             [1] => 2
//             [2] => 3
//         )
//     )
// )
```


<a name="method-start"></a>
### start() [^](#noop)
Launch `noop` app with the current configuration. In the registry, `app`, `request` and `controllers` arrays are populated, and the controllers scripts are included.

All configuration variables must be modified **before** this method.

After controllers inclusion, there is an implicit call to `noop::output()`.


<a name="method-status"></a>
### status( `$code`, `$status`[, `$content`[, `$type`]] ) [^](#noop)
Stop the current script, and return HTTP response to the client.

###### Parameters
- `$code` Required, Integer. HTTP response code
- `$status` Required , String. HTTP response status
- `$content` Optional, String. Body of the response. Default `''`
- `$type` Optional, String. A MIME type, or a shortcut. Default `'html'`



<a name="method-view"></a>
### view( `$name`[, `$data`] ) [^](#noop)
Compile the `$name` view.

###### Parameters
- `$name` Required, String. The relative path to the view file, minus ".php"
- `$data` Optional, Array. An additional data array to transmit to the view.

###### Return
- String. The compiled view (where variables are replaced with values)
