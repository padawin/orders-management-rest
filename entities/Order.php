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

	public static function createOrder($date, $vat)
	{
		$error = array();
		if (!is_float($vat) || !is_int($vat)) {
			$error['vat'] = "The vat must be an integer or a float";
		}
		if (time() > $date) {
			$error['date'] = "The order date must not be in the past";
		}

		if (!empty($errors)) {
			throw new \InvalidArgumentException(json_encode($errors));
		}

		return static::getModel()->insert(
			array(
				'date' => $date,
				'vat' => $vat,
				'status' => self::STATUS_DRAFT
			)
		);
	}
}
