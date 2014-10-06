<?php
namespace entities;

require_once "Entity.php";
require_once "Order.php";
require_once "Product.php";

/**
 * Line item's entity class.
 */
class LineItem extends Entity
{
	protected static $_model;

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
			throw new \InvalidArgumentException(json_encode($error));
		}

		return static::getModel()->getLineItems($idOrder, $idProduct);
	}

	public static function addLineItem(array $values)
	{
		$errors = array();
		if (!isset($values['id_order'])) {
			$errors['id_order'] = "An id_order is needed to create a line item";
		}
		else {
			$order = \entities\Order::getOrders(
				array('id_order' => $values['id_order'])
			);

			if (count($order) != 1) {
				$errors['id_order'] = "The id_order is not correct";
			}
			unset($order);
		}

		if (!isset($values['id_product'])) {
			$errors['id_product'] = "An id_product is needed to create a line item";
		}
		else if (!\entities\Order::existsWithId($values['id_order'])) {
			$errors['id_product'] = "The id_order is not correct";
		}

		$quantity = 1;
		if (isset($values['quantity'])) {
			if ($values['quantity'] != (int) $values['quantity'] || $values['quantity'] < 1) {
				$errors['quantity'] = "The quantity must be a positive integer";
			}
			else {
				$quantity = $values['quantity'];
			}
		}

		if (!empty($errors)) {
			throw new \InvalidArgumentException(json_encode($errors));
		}

		return static::getModel()->insert(
			array(
				'id_order' => $values['id_order'],
				'id_product' => $values['id_product'],
				'quantity' => $quantity
			)
		);
	}
}
