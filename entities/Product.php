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

	public static function existsWithId($idProduct)
	{
		return count(static::getModel()->get(array('id_product' => $idProduct))) == 1;
	}

	public static function updateProducts(array $values = array(), array $conditions = array())
	{
		return array(static::getModel()->update($values, $conditions));
	}

	public static function addProduct(array $values = array())
	{
		return static::getModel()->insert($values);
	}

	public static function deleteProducts(array $conditions = array())
	{
		return static::getModel()->delete($conditions);
	}
}
