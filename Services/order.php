<?php

namespace Services;

require_once "services/Service.php";
require_once "Entities/Order.php";
require_once "Models/Order/Sqlite.php";
require_once "Registry.php";

class order extends Service
{
	public function __construct()
	{
		\Entities\Order::setModel(new \Models\Order\Sqlite());
	}

	public function get(array $criterias = array())
	{
		return \Entities\Order::getOrders($criterias);
	}

	public function post(array $criterias = array())
	{
		$default = array(
			'date' => time(),
			'vat' => \Registry::get('default-vat')
		);

		$criterias = array_merge(
			$default,
			array_intersect_key(
				$criterias,
				$default
			)
		);

		return \Entities\Order::createOrder(
			$criterias['date'],
			$criterias['vat']
		);
	}

	public function put(array $values = array(), array $conditions = array())
	{
		return \Entities\Order::updateOrders($values, $conditions);
	}
}
