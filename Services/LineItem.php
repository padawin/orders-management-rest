<?php

namespace Services;

use \Entities\Order;
use \Entities\Product;
use \Entities\LineItem as LineItemEntity;
use \Models\Order\Sqlite as OrderModel;
use \Models\Product\Sqlite as ProductModel;
use \Models\LineItem\Sqlite as LineItemModel;

class LineItem extends Service
{
	public function __construct()
	{
		Order::setModel(new OrderModel());
		Product::setModel(new ProductModel());
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
