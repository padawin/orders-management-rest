<?php
namespace entities;

require_once "Entity.php";

/**
 * Order's entity class.
 */
class Order extends Entity
{
	const STATUS_DRAFT = 'DRAFT';
	const STATUS_PLACED = 'PLACED';
	const STATUS_PAID = 'PAID';
	const STATUS_CANCELLED = 'CANCELLED';

	protected static $_model;

	public static function getOrders(array $criterias = array())
	{
		return static::getModel()->get($criterias);
	}
}
