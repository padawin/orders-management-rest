<?php

namespace Services;

require_once "services/Service.php";
require_once "entities/LineItem.php";
require_once "models/LineItem/Sqlite.php";
require_once "models/Order/Sqlite.php";
require_once "models/Product/Sqlite.php";

class lineItem extends Service
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
