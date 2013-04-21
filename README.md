Silex: From nothing to an API
====
This is a markdown version of my slides, the actual slides can be found [here](http://www.slideshare.net/chrisdkemper/silex-from-nothing-to-an-api) Also, if you're going to run this code, don't forget to run [Composer](http://getcomposer.org/)!

## Introduction
	Silex: From nothing, to an API
	
## About me

I'm Chris Kemper

I work at Drummond Central, but I also freelance

I've been in the industry for around 5 years

I've written a book with a snake on the cover (It's on Version Control, if you didn't know)
	
I like pies - Pic

## What is Silex

"Silex is a PHP microframework for PHP 5.3. It is built on the shoulders of Symfony2 and Pimple and also inspired by sinatra."

Created by Fabien Potencier and Igor Wiedler

##Getting started
	
You can download it from the site directly
http://silex.sensiolabs.org/

Or, you can get with the times and use Composer

	
	{
    "require": {
        "silex/silex": "1.0.*@dev"
    	}
	}


### Your first end point

	require_once __DIR__.'/../vendor/autoload.php'; 

	$app = new Silex\Application(); 

	$app->get('/hello/{name}', function($name) use($app) { 
    	return 'Hello '.$app->escape($name); 
	}); 

	$app->run();

###Don't forget about .htaccess

Composer doesn’t pull it down, do don’t forget to put it in.

	<IfModule mod_rewrite.c>    	Options -MultiViews    	RewriteEngine On    	#RewriteBase /path/to/app    	RewriteCond %{REQUEST_FILENAME} !-f    	RewriteRule ^ index.php [L]	</IfModule>

If you’re using Apache 2.2.16+, then you can use:

	FallbackResource /index.php

###nginx works too!

	server {
    	#site root is redirected to the app boot script
    	location = / {
    	    try_files @site @site;
    	}

    	#all other locations try other files first and go to our front controller if none of them exists
    	location / {
    	    try_files $uri $uri/ @site;
    	}

    	#return 404 for all php files as we do have a front controller
    	location ~ \.php$ {
    	    return 404;
    	}

    	location @site {
    	    fastcgi_pass   unix:/var/run/php-fpm/www.sock;
    	    include fastcgi_params;
    	    fastcgi_param  SCRIPT_FILENAME $document_root/index.php;
    	    #uncomment when running via https
    	    #fastcgi_param HTTPS on;
    	}
	}
### Using a templating language.

Enter, Twig!

	{
    	"require": {
        	"silex/silex": "1.0.*@dev",
        	"twig/twig": ">=1.8,<2.0-dev",
        	"symfony/twig-bridge": "~2.1"
    	}
	}

### Register the Twig Service provider

	$app->register(new Silex\Provider\TwigServiceProvider(), array(
	    'twig.path' => __DIR__.'/views',
	));
	
And to use it.

	$app->get('/hello/{name}', function ($name) use ($app) {
    	return $app['twig']->render('hello.twig', array(
    	    'name' => $name,
    	));
	});

###So many more Service providers

Check the docs at http://silex.sensiolabs.org/documentation

There are a boat load of Built-in Service Providers which you can take advantage of!

Doctrine and Monolog are just a couple of examples.

###Some tips

There are a number of ways to use Silex for your application, but here are a couple of tips that may make things a little bit easier.

###Extending the base application

Autoload your own Class

	{
    	"require": {
        	"silex/silex": "1.0.*@dev",
        	"twig/twig": ">=1.8,<2.0-dev",
        	"symfony/twig-bridge": "~2.1"	
    	},
    	"autoload": {
    	    "psr-0": {"Acme": "src/"}
    	}
	}

	

Now create the application

	<?php
	namespace Acme;

	use 
		Silex\Application as BaseApplication;

	class Application extends BaseApplication {

	}


###Separate your config and routes

Rather than having your routes, and configuration in one file, split those guys up!

app/bootstrap.php
	
	<?php
	
	require_once __DIR__ . '/../vendor/autoload.php';

	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;

	use
		Acme\Application;

index.php
	
	<?php

	$app = include __DIR__ . '/../app/bootstrap.php';

	$app->run();

###Let's make an API

Silex is great for API's, so let's make one. Here is the groundwork for a basic user-based API

Some lovely endpoints:

* Create a user (/user) - This will use POST to create a new user in the DB.
* Update a user (/user/{id}) - This will use PUT to update the user
* View a user (/user/{id}) - This will use GET to view the users information
* Delete a user (/user/{id}) - Yes you guessed it, this uses the DELETE method.

You may notice all of these URL's are the same, that was on purpose!

###More dependencies!

If we want to use a database, we'll need to define one. Let's get DBAL!

	{
    	"require": {
        	"silex/silex": "1.0.*@dev",
        	"twig/twig": ">=1.8,<2.0-dev",
        	"symfony/twig-bridge": "~2.1",
        	"doctrine/dbal": "2.2.*"
    	},
    	"autoload": {
    	    "psr-0": {"Acme": "src/"}
    	}
	}

It'll just be MySQL so let's configure that

	$app->register(new DoctrineServiceProvider(), array(
		'db.options' => array(
			'dbname' 	=> 'acme',
			'user' 		=> 'root',
			'password' 	=> 'root',
			'host' 		=> 'localhost',
			'driver' 	=> 'pdo_mysql',
		),
	));

I've also created a user table in the DB with 3 fields: Id, email and name.

###POST

Before we can do anything, we need to create some users.

	$app->post('/user', function (Request $request) use ($app) {
    	$user = array(
	    	'email' => $request->get('email'),
	    	'name' => $request->get('name')
	    );

	    $app['db']->insert('user', $user);

	    return new Response("User " . $app['db']->lastInsertId() . " created", 201);
	});

###PUT

To update the users, we need to use PUT

	$app->put('/user/{id}', function (Request $request, $id) use ($app) {
	    $sql = "UPDATE user SET email = ?, name = ? WHERE id = ?";

	    $app['db']->executeUpdate($sql, array(
	    	$request->get('email'),
	    	$request->get('name'),
	    	(int) $id)
	    );
	
	    return new Response("User " . $id . " updated", 303);
	});
	
###GET

Let's get the API to output the user data as JSON

	$app->get('/user/{id}', function (Request $request, $id) use ($app) {
    	$sql = "SELECT * FROM user WHERE id = ?";
    	$post = $app['db']->fetchAssoc($sql, array((int) $id));

   		return $app->json($post, 201);
	});

###DELETE

Lastly, we have DELETE

	$app->delete('/user/{id}', function (Request $request, $id) use ($app) {
	    $sql = "DELETE FROM user WHERE id = ?";
	    $app['db']->executeUpdate($sql, array((int) $id));
	
	    return new Response("User " . $id . " deleted", 303);
	});

###A note about match

Using match, you can catch any method used on a route. Like so:
	
	$app->match('/user', function () use ($app) {
		...
	});
	
You can also limit the method accepted by match by using the 'match' method
	
	$app->match('/user', function () use ($app) {
		...
	})->method('PUT|POST');

###Using before and after

Each request, can be pre, or post processed. In this case, it could be used for auth.

	$before = function (Request $request) use ($app) {
		...
	};
	
	$after = function (Request $request) use ($app) {
		...
	};

	$app->match('/user', function () use ($app) {
		..
	})
	->before($before)
	->after($after);

This can also be used globally, like so:

	$app->before(function(Request $request) use ($app) {
		...
	});

You can also make an event be as early as possible, or as late as possible by using Application::EARLY_EVENT and Application::LATE_EVENT, respectively. 

	$app->before(function(Request $request) use ($app) {
		...
	}, Application::EARLY_EVENT);


###TIME for a demo

###Summary

This is just a small amount of what Silex is capable of, try it out.

Thank You

http://silex.sensiolabs.org/

