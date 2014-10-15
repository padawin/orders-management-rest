<?php

use \Exceptions\MethodNotAllowed;
use \Exceptions\Conflict;
use \Exceptions\BadRequest;
use \Exceptions\Duplicate;

function __autoload($class)
{
	$path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
	require_once($path . '.php');
}

set_include_path(realpath('../') . PATH_SEPARATOR . get_include_path());
require_once "config.php";

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
			parse_str(file_get_contents('php://input'), $criterias);
			echo json_encode(array($service->post($criterias)));
			break;
		case 'PUT':
			parse_str(file_get_contents('php://input'), $params);
			$values = json_decode(isset($params['values']) ? $params['values'] : '', true);
			$conditions = json_decode(isset($params['conditions']) ? $params['conditions'] : '', true);

			if (empty($values) || $conditions === false) {
				throw new \InvalidArgumentException("The conditions and values must be valid JSON values");
			}

			echo json_encode(array($service->put($values, $conditions)));
			break;
		case 'DELETE':
			$conditions = $_GET;
			unset($conditions['service']);

			echo json_encode(array($service->delete($conditions)));
			break;
		default:
			throw new \Exceptions\MethodNotAllowed("The requested method does not exist");
	}
}
catch (InvalidArgumentException $e) {
	header("HTTP/1.0 400 Bad Request", true, 400);
	echo json_encode(array($e->getMessage()));
}
catch (MethodNotAllowed $e) {
	header("HTTP/1.0 405 Method not allowed", true, 405);
	echo json_encode(array($e->getMessage()));
}
catch (Conflict $e) {
	header("HTTP/1.0 409 Conflict", true, 409);
	echo json_encode(array($e->getMessage()));
}
catch (BadRequest $e) {
	header("HTTP/1.0 400 Bad Request", true, 400);
	echo json_encode(array("Bad Request"));
}
catch (Duplicate $e) {
	header("HTTP/1.0 409 Duplicate content", true, 409);
	echo json_encode(array("Duplicate content"));
}
catch (Exception $e) {
	header("HTTP/1.0 500 Internal Server Error", true, 500);
	echo json_encode(array("Internal Server Error"));
}
