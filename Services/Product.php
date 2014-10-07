<?php

namespace Services;

class Product extends Service
{
	public function __construct()
	{
		\Entities\Product::setModel(new \Models\Product\Sqlite());
		\Entities\LineItem::setModel(new \Models\LineItem\Sqlite());
	}

	public function get(array $criterias = array())
	{
		return \Entities\Product::getProducts($criterias);
	}

	public function put(array $values = array(), array $conditions = array())
	{
		return \Entities\Product::updateProducts($values, $conditions);
	}

	public function post(array $values = array())
	{
		return \Entities\Product::addProduct($values);
	}

	public function delete(array $conditions = array())
	{
		return \Entities\Product::deleteProducts($conditions);
	}
}
