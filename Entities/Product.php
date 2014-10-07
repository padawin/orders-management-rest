<?php
namespace Entities;

use \InvalidArgumentException;
use \Exceptions\Conflict;

/**
 * Product's entity class.
 */
class Product extends Entity
{
	protected static $_model;

	/**
	 * Method to get a collection of products from some criterias.
	 *
	 * @param array $criterias Optional
	 * @return array Returns the list of products matching the criterias
	 */
	public static function getProducts(array $criterias = array())
	{
		return static::getModel()->get($criterias);
	}

	/**
	 * Checks if a product exists with the given order id.
	 *
	 * @param integer $idProduct
	 * @return boolean True if the product exists
	 */
	public static function existsWithId($idProduct)
	{
		return count(static::getModel()->get(array('id_product' => $idProduct))) == 1;
	}

	/**
	 * Update an (or multiple) existing line item(s)
	 *
	 * @param array $values New values the line items must have
	 * @param array $conditions (Optional) Conditions on the existing fields to
	 * 		update the desired rows, if not provided, every rows will be updated
	 * @return integer The number of updated rows
	 */
	public static function updateProducts(array $values = array(), array $conditions = array())
	{
		return static::getModel()->update($values, $conditions);
	}

	/**
	 * Add a product in the database.
	 *
	 * @param array $values
	 * @return integer The id of the inserted product
	 */
	public static function addProduct(array $values)
	{
		return static::getModel()->insert($values);
	}

	/**
	 * Delete one or multiple products for a given order from the database.
	 *
	 * @param array $conditions Conditions on the existing fields to delete the
	 * 		desired rows, if not provided, every rows will be deleted.
	 * @return int the number of deleted rows
	 */
	public static function deleteProducts(array $conditions = array())
	{
		$products = self::getProducts($conditions);
		if (empty($products)) {
			throw new InvalidArgumentException("No product found");
		}

		foreach ($products as $product) {
			if (LineItem::existsWithIdProduct($product['id_product'])) {
				throw new Conflict("Some products already belong to some orders and can't be deleted");
			}
		}

		return static::getModel()->delete($conditions);
	}
}
