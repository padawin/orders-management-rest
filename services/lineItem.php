<?php

namespace services;

require_once "services/Service.php";
require_once "entities/LineItem.php";
require_once "models/LineItem/Sqlite.php";
require_once "models/Order/Sqlite.php";
require_once "models/Product/Sqlite.php";

class lineItem extends Service
{
	public function __construct()
	{
		\entities\Order::setModel(new \models\Order\Sqlite());
		\entities\Product::setModel(new \models\Product\Sqlite());
		\entities\LineItem::setModel(new \models\LineItem\Sqlite());
	}

	public function get(array $criterias = array())
	{
		return \entities\LineItem::getLineItems($criterias);
	}

	public function post(array $values)
	{
		return \entities\LineItem::addLineItem($values);
	}
}
