<?php
namespace entities;

require_once "Entity.php";

/**
 * Product's entity class.
 */
class Product extends Entity
{
	protected static $_model;

	public static function getProducts(array $criterias = array())
	{
		return static::getModel()->get($criterias);
	}

	public static function updateProducts(array $values = array(), array $conditions = array())
	{
		return array(static::getModel()->update($values, $conditions));
	}

	public static function addProduct(array $values = array())
	{
		static::getModel()->insert($values);
		return array(true);
	}

	public static function deleteProducts(array $conditions = array())
	{
		return static::getModel()->delete($conditions);
	}
}
