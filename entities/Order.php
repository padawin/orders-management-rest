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

	public static function updateOrders(array $values = array(), array $conditions = array())
	{
		self::_check($values);
		return array(static::getModel()->update($values, $conditions));
	}

	public static function createOrder($date, $vat)
	{
		self::_check(array('date' => $date, 'vat' => $vat));
		return static::getModel()->insert(
			array(
				'date' => $date,
				'vat' => $vat,
				'status' => self::STATUS_DRAFT
			)
		);
	}

	protected static function _check(array $values)
	{
		$error = array();
		if (
			isset($values['vat'])
			&& (
				!is_float($values['vat'])
				|| !is_int($values['vat'])
			)
		) {
			$error['vat'] = "The vat must be an integer or a float";
		}

		if (isset($values['date'])) {
			if (!is_int($values['date'])) {
				$error['date'] = "The order date must be a valid timestamp";
			}
			else if (time() > $values['date']) {
				$error['date'] = "The order date must not be in the past";
			}
		}

		if (!empty($errors)) {
			throw new \InvalidArgumentException(json_encode($errors));
		}

	}
}
