<?php

// Override default config variables.
// Check noop source or noop::inspect('config') to see all of them
// You can also add your own app wide variables

noop::config( array(

	'default'=> array(
		'lang'=>'fr',
		'mime'=>'html',
	),
	'pdo'=>array(
		// 'db'=>'mysql,host=localhost;dbname=db,user,password'
	),
	'dev'=>array(
		'debug'=>TRUE,
	),

) );
