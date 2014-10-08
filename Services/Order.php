<?php

namespace Services;

use \Registry;
use \Entities\Order as OrderEntity;
use \Models\Order\Sqlite as OrderModel;

class Order extends Service
{
	/**
	 * Set the needed models to use the Order entity
	 */
	public function __construct()
	{
		OrderEntity::setModel(new OrderModel());
	}

	public function get(array $criterias = array())
	{
		return OrderEntity::getOrders($criterias);
	}

	/**
	 * Creates a new order. The default date is the current date and the
	 * default VAT is in the configuration, stored in the Registry
	 *
	 * @param array $criterias Values of the order to save
	 * @return the id of the inserted order
	 */
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
