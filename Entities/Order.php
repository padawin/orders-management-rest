<?php
namespace Entities;

use \InvalidArgumentException;

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

	public static function updateOrders(
		array $values = array(), array $conditions = array()
	)
	{
		self::_check($values);

		$orders = self::getOrders($conditions);
		if (empty($orders)) {
			throw new InvalidArgumentException("No order to update found");
		}

		if (isset($values['status'])) {
			foreach ($orders as $order) {
				self::_checkStatus($values, $order);
			}
		}
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
			throw new InvalidArgumentException(json_encode($errors));
		}
	}

	protected static function _checkStatuses(array $values, array $order)
	{
		if (
			$order['status'] == self::STATUS_DRAFT
			&& $values['status'] == self::STATUS_PLACED
			&& !LineItem::existsWithIdOrder($order['id_order'])
		) {
			throw new InvalidArgumentException(
				"An order can't be placed with no line item"
			);
		}
		else if (
			(
				$order['status'] == self::STATUS_DRAFT
				|| $order['status'] == self::STATUS_PLACED
			)
			&& $values['status'] == self::STATUS_CANCELLED
			&& empty($values['cancel_reason'])
		) {
			throw new InvalidArgumentException(
				"To cancel an order, a reason must be profided"
			);
		}
		else if (
			$order['status'] != self::STATUS_PLACED
			|| $values['status'] != self::STATUS_PAID
		) {
			throw new InvalidArgumentException(
				"Invalid status change combination"
			);
		}
	}

	public static function existsWithId($idOrder)
	{
		return static::getModel()->existsWithId($idOrder);
	}
}
