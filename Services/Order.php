<?php

namespace Services;

use \Registry;
use \Entities\Order as OrderEntity;
use \Model\Order\Sqlite as OrderModel;

class Order extends Service
{
	public function __construct()
	{
		OrderEntity::setModel(new OrderModel());
	}

	public function get(array $criterias = array())
	{
		return OrderEntity::getOrders($criterias);
	}

	public function post(array $criterias = array())
	{
		$default = array(
			'date' => time(),
			'vat' => Registry::get('default-vat')
		);

		$criterias = array_merge(
			$default,
			array_intersect_key(
				$criterias,
				$default
			)
		);

		return OrderEntity::createOrder(
			$criterias['date'],
			$criterias['vat']
		);
	}

	public function put(array $values = array(), array $conditions = array())
	{
		return OrderEntity::updateOrders($values, $conditions);
	}
}
