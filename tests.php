<?php

function __autoload($class)
{
	$path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
	require_once($path . '.php');
}

set_include_path(realpath('.') . PATH_SEPARATOR . get_include_path());
require_once "config.php";
Registry::set('db', 'database/orders-tests.db');

use \Services\Order as OrderService;
use \Services\Product as ProductService;
use \Services\LineItem as LineItemService;

$orderService = new OrderService();
$productService = new ProductService();
$lineItemService = new LineItemService();

$orders = $orderService->post(array());

try {
	$productService->post();
}
catch (\Exceptions\BadRequest $e) {
	var_dump("expected");
}
var_dump($orders);

$orders = $orderService->get(array());
var_dump($orders);
