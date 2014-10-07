<?php
namespace Entities;

use \InvalidArgumentException;
use \Entities\Order;
use \Entities\Product;

/**
 * Line item's entity class.
 * @TODO factorise the different checks?
 */
class LineItem extends Entity
{
	protected static $_model;

	/**
	 * Method to get a collection of line items from some criterias.
	 *
	 * @param array $criterias Optional
	 * @throws InvalidArgumentException If the criterias are not correct:
	 * 		- Invalid id_product or id_order,
	 * 		- Unexisting product or order
	 * @return array Returns the list of line items matching the criterias
	 */
	public static function getLineItems(array $criterias = array())
	{
		$error = array();
		$idOrder = null;
		if (isset($criterias['id_order'])) {
			if ($criterias['id_order'] == (int) $criterias['id_order']) {
				$idOrder = (int) $criterias['id_order'];
			}
			else {
				$error['id_order'] = "The id_order must be an integer";
			}
		}
		$idProduct = null;
		if (isset($criterias['id_product'])) {
			if ($criterias['id_product'] == (int) $criterias['id_product']) {
				$idProduct = (int) $criterias['id_product'];
			}
			else {
				$error['id_product'] = "The id_product must be an integer";
			}
		}

		if (!empty($error)) {
			throw new InvalidArgumentException(json_encode($error));
		}

		return static::getModel()->getLineItems($idOrder, $idProduct);
	}

	/**
	 * Add a line item in the database.
	 *
	 * @param array $values
	 * @throws InvalidArgumentException If no line item can be created with the
	 * 		provided values:
	 * 		- Invalid id_product or id_order,
	 * 		- Unexisting product or order,
	 * 		- Not draft order (can't be edited, and so no line item can be added
	 * 		for it)
	 * 		- The provided quantity is not a positive integer
	 * @return integer The id of the inserted line item
	 */
	public static function addLineItem(array $values)
	{
		$errors = array();
		if (!isset($values['id_order'])) {
			$errors['id_order'] = "An id_order is needed to create a line item";
		}
		else {
			$order = Order::get(
				array('id_order' => $values['id_order'])
			);
			if (count($order) == 0) {
				$errors['id_order'] = "The id_order is not correct";
			}
			else if ($order['status'] != Order::STATUS_DRAFT) {
				$errors['id_order'] = "An order can be edited only as a DRAFT";
			}
		}

		if (!isset($values['id_product'])) {
			$errors['id_product'] = "An id_product is needed to create a line item";
		}
		else if (!Product::existsWithId($values['id_product'])) {
			$errors['id_product'] = "The id_product is not correct";
		}

		$quantity = 1;
		if (isset($values['quantity'])) {
			if (
				$values['quantity'] != (int) $values['quantity']
				|| $values['quantity'] < 1
			) {
				$errors['quantity'] = "The quantity must be a positive integer";
			}
			else {
				$quantity = $values['quantity'];
			}
		}

		if (!empty($errors)) {
			throw new InvalidArgumentException(json_encode($errors));
		}

		return static::getModel()->insert(
			array(
				'id_order' => $values['id_order'],
				'id_product' => $values['id_product'],
				'quantity' => $quantity
			)
		);
	}

	/**
	 * Update an (or multiple) existing line item(s)
	 *
	 * @param array $values New values the line items must have
	 * @param array $conditions (Optional) Conditions on the existing fields to
	 * 		update the desired rows, if not provided, every rows will be updated
	 * @throws InvalidArgumentException If at least one line item matching the
	 * 		conditions belongs to a non-draft order
	 * @return integer The number of updated rows
	 */
	public static function updateLineItems(
		array $values = array(), array $conditions = array()
	)
	{
		$orders = self::getOrders($conditions);
		foreach ($orders as $order) {
			if ($order['status'] != Order::STATUS_DRAFT) {
				throw new InvalidArgumentException(
					"An order can be edited only as a DRAFT"
				);
			}
		}

		return static::getModel()->update($values, $conditions);
	}

	/**
	 * Delete one or multiple line items for a given order from the database.
	 *
	 * @param array $conditions Conditions on the existing fields to delete the
	 * 		desired rows, if not provided, every rows will be deleted.
	 * @throws InvalidArgumentException If the conditions do not respect the
	 * 		following cases:
	 * 		- no id_order is provided, or the id_order id not correct,
	 * 		- the matching order is not DRAFT,
	 * 		- a product id is provided but matches no product in the DB
	 * @return int the number of deleted rows
	 */
	public static function deleteLineItems(array $conditions)
	{
		$errors = array();
		if (!isset($conditions['id_order'])) {
			$errors['id_order'] = "An id_order is needed to delete a line item";
		}
		else {
			$order = Order::get(
				array('id_order' => $values['id_order'])
			);
			if (count($order) == 0) {
				$errors['id_order'] = "The id_order is not correct";
			}
			else if ($order['status'] != Order::STATUS_DRAFT) {
				$errors['id_order'] = "An order's line item can be deleted "
					. "only DRAFT orders";
			}
		}

		if (
			isset($conditions['id_product'])
			&& !Product::existsWithId($conditions['id_product'])
		) {
			$errors['id_product'] = "The id_product is not correct";
		}

		if (!empty($errors)) {
			throw new InvalidArgumentException(json_encode($errors));
		}

		return static::getModel()->delete($conditions);
	}

	/**
	 * Checks if a line item exists with a given product id;
	 *
	 * @param integer $idProduct
	 * @return boolean True if at least one row is found
	 */
	public static function existsWithIdProduct($idProduct)
	{
		$lineItems = static::getModel()->get(array('id_product' => $idProduct));
		return count($lineItems) > 0;
	}


	/**
	 * Checks if a line item exists with a given order id;
	 *
	 * @param integer $idOrder
	 * @return boolean True if at least one row is found
	 */
	public static function existsWithIdOrder($idOrder)
	{
		$lineItems = static::getModel()->get(array('id_order' => $idOrder));
		return count($lineItems) > 0;
	}
}
