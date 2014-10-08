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
use \Entities\Order as OrderEntity;

class Tests
{
	public function setup()
	{
		exec("echo > " . Registry::get('db'));
		exec("./INSTALL " . 'orders-tests.db');
	}

	// Users need to be able to maintain a list of products
	public function testProduct()
	{
		$productService = new ProductService();
		try {
			echo "Creation of an empty product should fail: ";
			$productService->post();
			echo "\033[01;31mKO\033[0m\n";
		}
		catch (\Exceptions\BadRequest $e) {
			echo "\033[01;32mOK\033[0m\n";
		}

		$myProduct = $productService->post(array("name" => "my fist product", "price" => 20));
		echo "The newly created product id should be 1: ",
			$myProduct == 1 ? "\033[01;32mOK\033[0m" : "\033[01;31mKO\033[0m", "\n";

		$myProduct = $productService->get(array("id_product" => 1));
		echo "The product's name should be \"my fist product\": ",
			$myProduct[0]['name'] == "my fist product" ? "\033[01;32mOK\033[0m" : "\033[01;31mKO\033[0m", "\n";

		$updatedRows = $productService->put(array("name" => "new name"), array("id_product" => 1));
		echo "1 Row should have been edited: ",
			$updatedRows == 1 ? "\033[01;32mOK\033[0m" : "\033[01;31mKO\033[0m", "\n";

		$nbDeleted = $productService->delete(array("id_product" => 1));
		echo "1 Row should have been deleted: ",
			$nbDeleted == 1 ? "\033[01;32mOK\033[0m" : "\033[01;31mKO\033[0m", "\n";

		$myProduct = $productService->get(array("id_product" => 1));
		echo "No product is supposed to be found: ",
			empty($myProduct) ? "\033[01;32mOK\033[0m" : "\033[01;31mKO\033[0m", "\n";
	}

	public function testUniqueValuesProduct()
	{
		$productService = new ProductService();
		$productService->post(array("name" => "my fist product", "price" => 20));
		echo "Try to create a duplicate product: ";
		try {
			$productService->post(array("name" => "my fist product", "price" => 20));
			echo "Product created, \033[01;31mKO\033[0m\n";
		}
		catch (Exceptions\Duplicate $e) {
			echo "Product not created, \033[01;32mOK\033[0m\n";
		}
	}

	public function testAddEditDraftOrders()
	{
		$orderService = new OrderService();
		$orderId = $orderService->post();
		echo "The newly created order id should be 1: ",
			$orderId == 1 ? "\033[01;32mOK\033[0m" : "\033[01;31mKO\033[0m", "\n";

		$order = $orderService->get(array('id_order' => 1));
		echo "The order's vat should be .2: ",
			((float) $order[0]['vat'] == .2) ? "\033[01;32mOK\033[0m" : "\033[01;31mKO\033[0m", "\n";
		echo "The order's status should be DRAFT: ",
			((float) $order[0]['status'] == 'DRAFT') ? "\033[01;32mOK\033[0m" : "\033[01;31mKO\033[0m", "\n";

		echo "Creating an order in the past should fail: ";
		try {
			$orderId = $orderService->post(array('date' => 1000));
			echo "\033[01;31mKO\033[0m\n";
		}
		catch (InvalidArgumentException $e) {
			echo "\033[01;32mOK\033[0m\n";
		}

		$updatedRows = $orderService->put(array("vat" => .5), array('id_order' => 1));
		$order = $orderService->get(array('id_order' => 1));
		echo "The order's vat should be .5: ",
			((float) $order[0]['vat'] == .5) ? "\033[01;32mOK\033[0m" : "\033[01;31mKO\033[0m", "\n";
	}

	public function testDeleteOrder()
	{
		$orderService = new OrderService();
		$orderId = $orderService->post();
		echo "An order should not be deletable: ";
		try {
			$orderService->delete(array('id_order' => $orderId));
			echo "\033[01;31mKO\033[0m\n";
		}
		catch (Exceptions\MethodNotAllowed $e) {
			echo "\033[01;32mOK\033[0m\n";
		}
	}

	public function testLineItems()
	{
		$orderService = new OrderService();
		$lineItemService = new LineItemService();
		$productService = new ProductService();
		$orderService->post();
		$productService->post(array("name" => "my fist product", "price" => 20));
		$productService->post(array("name" => "my second product", "price" => 20));
		$lineItemService->post(array('id_order' => 1, 'id_product' => 1));
		$lineItemService->post(array('id_order' => 1, 'id_product' => 2));

		$order = $orderService->get(array('id_order' => 1));
		echo "The net price must be available in the order: ", isset($order[0]['net_price']) ? "\033[01;32mOK\033[0m" : "\033[01;31mKO\033[0m", "\n";
		echo "The gross price must be available in the order: ", isset($order[0]['gross_price']) ? "\033[01;32mOK\033[0m" : "\033[01;31mKO\033[0m", "\n";

		$lineItems = $lineItemService->get(array('id_order' => 1));
		echo "2 line items are fetched: ", count($lineItems) == 2 ? "\033[01;32mOK\033[0m" : "\033[01;31mKO\033[0m", "\n";
		foreach ($lineItems as $li) {
			echo "The line item quantity is supposed to be 1: ", $li['quantity'] == 1 ? "\033[01;32mOK\033[0m" : "\033[01;31mKO\033[0m", "\n";
			echo "The product name is available: ", isset($li['name']) ?  "\033[01;32mOK\033[0m" : "\033[01;31mKO\033[0m", "\n";
		}

		echo "Try to update a line item with a negative quantity: ";
		try {
			$lineItemService->put(array('quantity' => -1), array('id_line_item' => 1));
			echo "\033[01;31mKO\033[0m\n";
		}
		catch (InvalidArgumentException $e) {
			echo "\033[01;32mOK\033[0m\n";
		}

		$lineItemService->delete(array('id_line_item' => 1, 'id_order' => 1));
		$lineItems = $lineItemService->get(array('id_order' => 1));
		echo "1 line items are fetched: ", count($lineItems) == 1 ? "\033[01;32mOK\033[0m" : "\033[01;31mKO\033[0m", "\n";
	}

	public function testDeleteOrderedItem()
	{
		$orderService = new OrderService();
		$lineItemService = new LineItemService();
		$productService = new ProductService();
		$orderService->post();
		$productService->post(array("name" => "my fist product", "price" => 20));
		$lineItemService->post(array('id_order' => 1, 'id_product' => 1));

		echo "Try to delete an already ordered product: ";
		try {
			$productService->delete(array('id_product' => 1));
			echo "\033[01;31mKO\033[0m\n";
		}
		catch (Exceptions\Conflict $e) {
			echo "\033[01;32mOK\033[0m\n";
		}
	}

	public function testOrdersStatusesCancelled()
	{
		$orderService = new OrderService();
		$lineItemService = new LineItemService();
		$productService = new ProductService();
		$orderService->post();

		echo "Try to place an order with no item: ";
		try {
			$orderService->put(array('status' => OrderEntity::STATUS_PLACED), array('id_order' => 1));
			echo "\033[01;31mKO\033[0m\n";
		}
		catch (InvalidArgumentException $e) {
			echo "\033[01;32mOK\033[0m\n";
		}

		$productService->post(array("name" => "my fist product", "price" => 20));
		$lineItemService->post(array('id_order' => 1, 'id_product' => 1));
		echo "Try to place an order with an item: ";
		try {
			$orderService->put(array('status' => OrderEntity::STATUS_PLACED), array('id_order' => 1));
			echo "\033[01;32mOK\033[0m\n";
		}
		catch (Exception $e) {
			echo "\033[01;31mKO\033[0m\n";
		}

		echo "Try to cancel an order with no reason: ";
		try {
			$orderService->put(array('status' => OrderEntity::STATUS_CANCELLED), array('id_order' => 1));
			echo "\033[01;31mKO\033[0m\n";
		}
		catch (Exception $e) {
			echo "\033[01;32mOK\033[0m\n";
		}

		echo "Try to cancel an order with a reason: ";
		try {
			$orderService->put(array('cancel_reason' => 'Some reason', 'status' => OrderEntity::STATUS_CANCELLED), array('id_order' => 1));
			echo "\033[01;32mOK\033[0m\n";
		}
		catch (Exception $e) {
			echo "\033[01;31mKO\033[0m\n";
		}
	}

	public function testOrdersStatusesPaid()
	{
		$orderService = new OrderService();
		$lineItemService = new LineItemService();
		$productService = new ProductService();
		$orderService->post();
		$productService->post(array("name" => "my fist product", "price" => 20));
		$lineItemService->post(array('id_order' => 1, 'id_product' => 1));
		$orderService->put(array('status' => OrderEntity::STATUS_PLACED), array('id_order' => 1));
		$orderService->put(array('status' => OrderEntity::STATUS_PAID), array('id_order' => 1));
		$order = $orderService->get(array('id_order' => 1));
		echo "The order must be paid: ";
		if ($order[0]['status'] == OrderEntity::STATUS_PAID) {
			echo "\033[01;32mOK\033[0m\n";
		}
		else {
			echo "\033[01;31mKO\033[0m\n";
		}
	}

	public function testOrdersStatusesBackToDraft()
	{
		$orderService = new OrderService();
		$lineItemService = new LineItemService();
		$productService = new ProductService();
		$orderService->post();
		$productService->post(array("name" => "my fist product", "price" => 20));
		$lineItemService->post(array('id_order' => 1, 'id_product' => 1));
		$orderService->put(array('status' => OrderEntity::STATUS_PLACED), array('id_order' => 1));
		echo "Try invalid status change: ";
		try {
			$orderService->put(array('status' => OrderEntity::STATUS_DRAFT), array('id_order' => 1));
			echo "\033[01;31mKO\033[0m\n";
		}
		catch (InvalidArgumentException $e) {
			echo "\033[01;32mOK\033[0m\n";
		}
	}

	public function testAddEditNonOrders()
	{
		$orderService = new OrderService();
		$lineItemService = new LineItemService();
		$productService = new ProductService();
		$orderService->post();
		$productService->post(array("name" => "my fist product", "price" => 20));
		$lineItemService->post(array('id_order' => 1, 'id_product' => 1));
		$orderService->put(array('status' => OrderEntity::STATUS_PLACED), array('id_order' => 1));

		echo "Try to add a line item to a non draft order: ";
		try {
			$lineItemService->post(array('id_order' => 1, 'id_product' => 1));
			echo "\033[01;31mKO\033[0m\n";
		}
		catch (InvalidArgumentException $e) {
			echo "\033[01;32mOK\033[0m\n";
		}
		echo "Try to update a non draft order: ";
		try {
			$updatedRows = $orderService->put(array("vat" => .5), array('id_order' => 1));
			echo "\033[01;31mKO\033[0m\n";
		}
		catch (InvalidArgumentException $e) {
			echo "\033[01;32mOK\033[0m\n";
		}
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
