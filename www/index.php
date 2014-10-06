<?php

set_include_path(realpath('../') . PATH_SEPARATOR . get_include_path());
require_once "config.php";
require_once "Registry.php";
require_once "exceptions/BadRequest.php";
require_once "exceptions/Duplicate.php";

header('Content-Type: application/json');

// no service provided
if (!isset($_GET['service'])) {
	header("HTTP/1.0 400 Bad Request", true, 400);
	echo "Bad Request";
	exit(1);
}

$method = $_SERVER['REQUEST_METHOD'];
$service = $_GET['service'];
$root = Registry::get('root');
$servicePath = realpath(Registry::get('root') . '/services/' . $service . '.php');

// unexisting service
if (!$servicePath || strpos($servicePath, $root . '/services/') != 0) {
	header("HTTP/1.0 400 Bad Request", true, 400);
	echo "Bad Request";
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
		case 'POST':
			$criterias = $_GET;
			unset($criterias['service']);
			echo json_encode($service->post($criterias));
			break;
		case 'PUT':
			$values = json_decode(isset($_GET['values']) ? $_GET['values'] : '', true);
			$conditions = array();
			if (isset($_GET['conditions'])) {
				$conditions = json_decode($_GET['conditions'], true);
			}

			if ($values === false || $conditions === false) {
				throw new \exceptions\BadRequest;
			}

			echo json_encode($service->put($values, $conditions));
			break;
		case 'DELETE':
			$conditions = array();
			if (isset($_GET['conditions'])) {
				$conditions = json_decode($_GET['conditions'], true);
			}

			if ($conditions === false) {
				throw new \exceptions\BadRequest;
			}

			echo json_encode($service->delete($conditions));
			break;
		default:
			throw new \exceptions\BadRequest;
	}
}
catch (InvalidArgumentException $e) {
	header("HTTP/1.0 400 Bad Request", true, 400);
	echo $e->getMessage();
}
catch (\exceptions\MethodNotAllowed $e) {
	header("HTTP/1.0 405 Method not allowed Request", true, 400);
	echo $e->getMessage();
}
catch (\exceptions\BadRequest $e) {
	header("HTTP/1.0 400 Bad Request", true, 400);
	echo "Bad Request";
}
catch (\exceptions\Duplicate $e) {
	header("HTTP/1.0 409 Duplicate content", true, 400);
	echo "Duplicate content";
}
catch (\Exception $e) {
	var_dump($e);
	header("HTTP/1.0 500 Internal Server Error");
	echo "Internal Server Error";
}
