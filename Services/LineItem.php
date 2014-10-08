<?php

namespace Services;

use \Entities\Order as OrderEntity;
use \Entities\Product as ProductEntity;
use \Entities\LineItem as LineItemEntity;
use \Models\Order\Sqlite as OrderModel;
use \Models\Product\Sqlite as ProductModel;
use \Models\LineItem\Sqlite as LineItemModel;

class LineItem extends Service
{
	/**
	 * Set the needed models to use the LineItem entity
	 */
	public function __construct()
	{
		OrderEntity::setModel(new OrderModel());
		ProductEntity::setModel(new ProductModel());
		LineItemEntity::setModel(new LineItemModel());
	}

	public function get(array $criterias = array())
	{
		return LineItemEntity::getLineItems($criterias);
	}

	public function post(array $values)
	{
		return LineItemEntity::addLineItem($values);
	}

	public function put(array $values = array(), array $conditions = array())
	{
		return LineItemEntity::updateLineItems($values, $conditions);
	}

	public function delete(array $conditions)
	{
		return LineItemEntity::deleteLineItems($conditions);
	}
}
