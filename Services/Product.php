<?php

namespace Services;

use \Entities\Product as ProductEntity;
use \Entities\LineItem;
use \Models\Product\Sqlite as ProductModel;
use \Models\LineItem\Sqlite as LineItemModel;

class Product extends Service
{
	/**
	 * Set the needed models to use the Product entity
	 */
	public function __construct()
	{
		ProductEntity::setModel(new ProductModel());
		LineItem::setModel(new LineItemModel());
	}

	public function get(array $criterias = array())
	{
		return ProductEntity::getProducts($criterias);
	}

	public function put(array $values = array(), array $conditions = array())
	{
		return ProductEntity::updateProducts($values, $conditions);
	}

	public function post(array $values = array())
	{
		return ProductEntity::addProduct($values);
	}

	public function delete(array $conditions = array())
	{
		return ProductEntity::deleteProducts($conditions);
	}
}
