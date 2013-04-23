<?php

require_once __DIR__ . '/../vendor/autoload.php';

use 
	Symfony\Component\HttpFoundation\Request,
	Symfony\Component\HttpFoundation\Response;

use
	Acme\Application;

$app = new Application; 

$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => __DIR__.'/views',
));

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
	'db.options' => array(
		'dbname' 	=> 'acme',
		'user' 		=> 'root',
		'password' 	=> 'root',
		'host' 		=> 'localhost',
		'driver' 	=> 'pdo_mysql',
	),
));

return $app;