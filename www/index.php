<?php

$config = require "../config.php";

// no service provided
if (!isset($_GET['service'])) {
	header("HTTP/1.0 400 Bad Request");
	exit(1);
}

$method = $_SERVER['REQUEST_METHOD'];
$service = $_GET['service'];
$servicePath = realpath($config['root'] . '/services/' . $service . '.php');

// unexisting service
if (!$servicePath || strpos($servicePath, $config['root'] . '/services/') != 0) {
	header("HTTP/1.0 400 Bad Request");
	exit(1);
}

require $servicePath;
$serviceClass = "\services\\" . $service;
$service = new $serviceClass();
try {
	switch ($method) {
		case 'GET':
			$criterias = $_GET;
			unset($criterias['service']);
			$service->get($criterias);
			break;
		default:
			header("HTTP/1.0 400 Bad Request");
			exit(1);
	}
}
catch (\Exception $e) {
	var_dump($e);
	header("HTTP/1.0 500 Internal Server Error");
	exit(1);
}
