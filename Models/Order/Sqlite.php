<?php

namespace Models\Order;

class Sqlite extends \Models\Sqlite
{
	protected static $_table = 'orders';
	protected static $_updatableFields = array(
		'vat', 'date', 'status', 'cancel_reason'
	);

	/**
	 * Returns the orders from the database matching $criterias
	 *
	 * @param array $criterias
	 * @return array the matching orders
	 */
	public function get(array $criterias = array())
	{
		$sql = "
			SELECT
				" . self::$_table . ".id_order,
				vat,
				date,
				status,
				cancel_reason,
				date_creation,
				SUM(price * quantity) AS net_price,
				SUM(price * quantity) + SUM(price * quantity) * vat AS gross_price
			FROM "
				. self::$_table
				. ' LEFT JOIN line_item ON ' . self::$_table . '.id_order = line_item.id_order
				LEFT JOIN product ON product.id_product = line_item.id_product'
			. (!empty($criterias) ? " WHERE " : "");

		$where = array();
		foreach ($criterias as $name => $value) {
			$where[] = self::$_table . '.' . $name . " = :" . $name;
		}

		$sql .= implode(" AND ", $where);

		$sql .= "
		GROUP BY
			orders.id_order,
			vat,
			date,
			status,
			cancel_reason,
			date_creation
		";
		$stmt = $this->_execute($sql, $criterias)[0];
		return $stmt->fetchAll();
	}

	/**
	 * Checks if an order exists with the provided id.
	 *
	 * @param integer $idOrder
	 * @return boolean True if the order exists
	 */
	public function existsWithId($idOrder)
	{
		$sql = "
			SELECT
				COUNT(1) AS count
			FROM "
				. self::$_table
			. ' WHERE id_order = ?';

		$stmt = $this->_execute($sql, array($idOrder))[0];
		$res = $stmt->fetchAll();
		return $res[0]['count'] > 0;
	}
}
