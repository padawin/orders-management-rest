<?php

namespace Models\LineItem;

class Sqlite extends \Models\Sqlite
{
	protected static $_table = 'line_item';
	protected static $_updatableFields = array('quantity');

	/**
	 * Fetch the line items from the database according to an order id and a
	 * product id
	 *
	 * @param integer $idOrder
	 * @param integer $idProduct
	 * @return array the matching line items
	 */
	public function getLineItems($idOrder = null, $idProduct = null)
	{
		$sql = "
		SELECT
			id_line_item,
			li.id_order,
			li.id_product,
			name,
			quantity,
			price
		FROM
			line_item AS li
			JOIN orders ON li.id_order = orders.id_order
			JOIN product ON li.id_product = product.id_product
		";

		$params = $where = array();
		if ($idOrder != null) {
			$where[] = "li.id_order = ?";
			$params[] = $idOrder;
		}

		if ($idProduct != null) {
			$where[] = "li.id_product = ?";
			$params[] = $idProduct;
		}

		if (!empty($where)) {
			$sql .= "WHERE " . implode(" AND ", $where);
		}

		$stmt = $this->_execute($sql, $params)[0];
		return $stmt->fetchAll();
	}
}
