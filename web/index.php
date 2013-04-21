<?php

use 
	Symfony\Component\HttpFoundation\Request,
	Symfony\Component\HttpFoundation\Response;

$app = include __DIR__ . '/../app/bootstrap.php';

$app->post('/user', function (Request $request) use ($app) {
    $user = array(
    	'email' => $request->get('email'),
    	'name' => $request->get('name')
    );

    $app['db']->insert('user', $user);

    return new Response("User " . $app['db']->lastInsertId() . " created", 201);
});

$app->get('{id}', function (Request $request, $id) use ($app) {
    $sql = "SELECT * FROM user WHERE id = ?";
    $post = $app['db']->fetchAssoc($sql, array((int) $id));

    return $app->json($post, 201);
});

$app->delete('/user/{id}', function (Request $request, $id) use ($app) {
    $sql = "DELETE FROM user WHERE id = ?";
    $app['db']->executeUpdate($sql, array((int) $id));

    return new Response("User " . $id . " deleted", 303);
});

$app->put('/user/{id}', function (Request $request, $id) use ($app) {
    $sql = "UPDATE user SET email = ?, name = ? WHERE id = ?";

    $app['db']->executeUpdate($sql, array(
    	$request->get('email'),
    	$request->get('name'),
    	(int) $id)
    );

    return new Response("User " . $id . " updated", 303);
});

$app->run();