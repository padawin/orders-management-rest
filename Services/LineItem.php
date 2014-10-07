<?php

namespace Services;

class LineItem extends Service
{
	public function __construct()
	{
		\Entities\Order::setModel(new \Models\Order\Sqlite());
		\Entities\Product::setModel(new \Models\Product\Sqlite());
		\Entities\LineItem::setModel(new \Models\LineItem\Sqlite());
	}

	public function get(array $criterias = array())
	{
		return \Entities\LineItem::getLineItems($criterias);
	}

	public function post(array $values)
	{
		return \Entities\LineItem::addLineItem($values);
	}

	public function put(array $values = array(), array $conditions = array())
	{
		return \Entities\LineItem::updateLineItems($values, $conditions);
	}

	public function delete(array $conditions)
	{
		return \Entities\LineItem::deleteLineItems($conditions);
	}
}
