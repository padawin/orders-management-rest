<?php

namespace services;

require_once "services/Service.php";
require_once "entities/Product.php";
require_once "entities/LineItem.php";
require_once "models/Product/Sqlite.php";
require_once "models/LineItem/Sqlite.php";

class product extends Service
{
	public function __construct()
	{
		\entities\Product::setModel(new \models\Product\Sqlite());
		\entities\LineItem::setModel(new \models\LineItem\Sqlite());
	}

	public function get(array $criterias = array())
	{
		return \entities\Product::getProducts($criterias);
	}

	public function put(array $values = array(), array $conditions = array())
	{
		return \entities\Product::updateProducts($values, $conditions);
	}

	public function post(array $values = array())
	{
		return \entities\Product::addProduct($values);
	}

	public function delete(array $conditions = array())
	{
		return \entities\Product::deleteProducts($conditions);
	}
}
