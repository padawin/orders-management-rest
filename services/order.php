<?php

namespace services;

require_once "services/Service.php";
require_once "entities/Order.php";
require_once "models/Order/Sqlite.php";
require_once "Registry.php";

class order extends Service
{
	public function __construct()
	{
		\entities\Order::setModel(new \models\Order\Sqlite());
	}

	public function get(array $criterias = array())
	{
		return \entities\Order::getOrders($criterias);
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

		return \entities\Order::createOrder(
			$criterias['date'],
			$criterias['vat']
		);
	}
}
