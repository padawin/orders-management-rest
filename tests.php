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

class Tests
{
	public function setup()
	{
		if (is_file(Registry::get('db'))) {
			unlink(Registry::get('db'));
			exec("./INSTALL " . 'orders-tests.db');
		}
	}

	// Users need to be able to maintain a list of products
	public function test1()
	{
		$productService = new ProductService();
		try {
			echo "Creation of an empty product should fail: ";
			$productService->post();
			echo "KO\n";
		}
		catch (\Exceptions\BadRequest $e) {
			echo "OK\n";
		}

		$myProduct = $productService->post(array("name" => "my fist product", "price" => 20));
		echo "The newly created product id should be 1: ",
			$myProduct == 1 ? "OK" : "KO", "\n";

		$myProduct = $productService->get(array("id_product" => 1));
		echo "The product's name should be \"my fist product\": ",
			$myProduct[0]['name'] == "my fist product" ? 'OK' : 'KO', "\n";

		$updatedRows = $productService->put(array("name" => "new name"), array("id_product" => 1));
		echo "1 Row should have been edited: ",
			$updatedRows == 1 ? "OK" : "KO", "\n";

		$nbDeleted = $productService->delete(array("id_product" => 1));
		echo "1 Row should have been deleted: ",
			$nbDeleted == 1 ? "OK" : "KO", "\n";

		$myProduct = $productService->get(array("id_product" => 1));
		echo "Np product is supposed to be found: ",
			empty($myProduct) ? 'OK' : 'KO', "\n";
	}

	public function run()
	{
		$methods = get_class_methods($this);
		foreach ($methods as $method) {
			if (strpos($method, 'test') === 0) {
				$this->setup();
				$this->$method();
			}
		}
	}
}

$tests = new Tests();
$tests->run();
