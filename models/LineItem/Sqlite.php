<?php

namespace models\LineItem;

require_once "models/Sqlite.php";

class Sqlite extends \models\Sqlite
{
	protected static $_table = 'line_item';
	protected static $_updatableFields = array('quantity');

	public function getLineItems($idOrder = null, $idProduct = null)
	{
		$sql = "
		SELECT
			id_line_item,
			li.id_order,
			li.id_product,
			name,
			quantity
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
