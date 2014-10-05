<?php

namespace services;

require_once "services/Service.php";
require_once "entities/Order.php";
require_once "models/Order/Sqlite.php";

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
}
