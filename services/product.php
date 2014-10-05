<?php

namespace services;

require_once "services/Service.php";
require_once "entities/Product.php";
require_once "models/Product/Sqlite.php";

class product extends Service
{
	public function __construct()
	{
		\entities\Product::setModel(new \models\Product\Sqlite());
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
