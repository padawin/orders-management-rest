<?php

set_include_path(realpath('../') . PATH_SEPARATOR . get_include_path());
require_once "config.php";
require_once "Registry.php";

header('Content-Type: application/json');

// no service provided
if (!isset($_GET['service'])) {
	header("HTTP/1.0 400 Bad Request");
	exit(1);
}

$method = $_SERVER['REQUEST_METHOD'];
$service = $_GET['service'];
$root = Registry::get('root');
$servicePath = realpath(Registry::get('root') . '/services/' . $service . '.php');

// unexisting service
if (!$servicePath || strpos($servicePath, $root . '/services/') != 0) {
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
			echo json_encode($service->get($criterias));
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
