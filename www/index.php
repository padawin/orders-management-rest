<?php

set_include_path(realpath('../') . PATH_SEPARATOR . get_include_path());
require_once "config.php";
require_once "Registry.php";
require_once "Exceptions/BadRequest.php";
require_once "Exceptions/Duplicate.php";
require_once "Exceptions/Conflict.php";
require_once "Exceptions/MethodNotAllowed.php";

header('Content-Type: application/json');

// no service provided
if (!isset($_GET['service'])) {
	header("HTTP/1.0 400 Bad Request", true, 400);
	echo "Bad Request";
	exit(1);
}

$method = $_SERVER['REQUEST_METHOD'];
$service = ucfirst($_GET['service']);
$root = Registry::get('root');
$servicePath = realpath(Registry::get('root') . '/Services/' . $service . '.php');

// unexisting service
if (!$servicePath || strpos($servicePath, $root . '/Services/') != 0) {
	header("HTTP/1.0 400 Bad Request", true, 400);
	echo "Bad Request";
	exit(1);
}

require $servicePath;
$serviceClass = "\Services\\" . $service;
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

			if (empty($values) || $conditions === false) {
				throw new \InvalidArgumentException("The conditions and values must be valid JSON values");
			}

			echo json_encode($service->put($values, $conditions));
			break;
		case 'DELETE':
			$conditions = $_GET;
			unset($conditions['service']);

			echo json_encode($service->delete($conditions));
			break;
		default:
			throw new \Exceptions\MethodNotAllowed("The requested method does not exist");
	}
}
catch (InvalidArgumentException $e) {
	header("HTTP/1.0 400 Bad Request", true, 400);
	echo $e->getMessage();
}
catch (\Exceptions\MethodNotAllowed $e) {
	header("HTTP/1.0 405 Method not allowed", true, 405);
	echo $e->getMessage();
}
catch (\Exceptions\Conflict $e) {
	header("HTTP/1.0 409 Conflict", true, 409);
	echo $e->getMessage();
}
catch (\Exceptions\BadRequest $e) {
	header("HTTP/1.0 400 Bad Request", true, 400);
	echo "Bad Request";
}
catch (\Exceptions\Duplicate $e) {
	header("HTTP/1.0 409 Duplicate content", true, 409);
	echo "Duplicate content";
}
catch (\Exception $e) {
	var_dump($e);
	header("HTTP/1.0 500 Internal Server Error", true, 500);
	echo "Internal Server Error";
}
