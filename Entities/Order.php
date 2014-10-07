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

	/**
	 * Method to get a collection of orders from some criterias.
	 *
	 * @param array $criterias Optional
	 * @return array Returns the list of orders matching the criterias
	 */
	public static function getOrders(array $criterias = array())
	{
		return static::getModel()->get($criterias);
	}

	/**
	 * Update an (or multiple) existing order(s)
	 *
	 * @param array $values New values the orders must have
	 * @param array $conditions (Optional) Conditions on the existing fields to
	 * 		update the desired rows, if not provided, every rows will be updated
	 * @throws InvalidArgumentException If the conditions match no order in the
	 *		database. See also _check() method
	 * @return integer The number of updated rows
	 */
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

	/**
	 * Add an order in the database.
	 *
	 * @param string $date The order date
	 * @param float $vat The order VAT
	 * @throws InvalidArgumentException See _check() method
	 * @return integer The id of the inserted order
	 */
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

	/**
	 * Check if the given values are valid to be saved in an order row.
	 * Used only to propagate an exception.
	 *
	 * @param array $values
	 * @throws InvalidArgumentException If the VAT is not a valid int or float,
	 *		if the date is not a valid timestamp or if the date is in the past
	 */
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

	/**
	 * Checks the new status of the order against the order's state in the
	 * database.
	 * From the specs, here are the rules:
	 * Orders can have the following statuses: DRAFT, PLACED, PAID, CANCELLED
	 * Newly created orders must be DRAFT
	 * An order's status can be "bumped" order from DRAFT to PLACED, but only if there is at least one line item
	 * An order's status can change from DRAFT to CANCELLED, when this happens a short reason must be provided
	 * An order's status can change from PLACED to PAID
	 * An order's status can change from PLACED to CANCELLED, when this happens a short reason must be provided
	 * No other other status changes are permitted, e.g. an order's status can never change back to DRAFT
	 * Changes can be made to orders, including adding/editing/deleting line items, while the orders are DRAFTs
	 * Changes should not be permitted once the status is either PLACED, PAID or CANCELLED, neither to the order itself nor its line items
	 *
	 * @throws InvalidArgumentException If the new status can't be applied
	 */
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

	/**
	 * Checks if an order exists with the given order id.
	 *
	 * @param integer $idOrder
	 * @return boolean True if the order exists
	 */
	public static function existsWithId($idOrder)
	{
		return static::getModel()->existsWithId($idOrder);
	}
}
