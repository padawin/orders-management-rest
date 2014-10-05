<?php
namespace entities;

require_once "Entity.php";

/**
 * Order's entity class.
 */
class Order extends Entity
{
	protected static $_model;

	public static function getOrders(array $criterias = array())
	{
		return static::getModel()->get($criterias);
	}
}
