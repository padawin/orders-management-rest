<?php

namespace models;

require_once "models/Model.php";
require_once "Registry.php";

class Sqlite implements Model
{
	protected static $_table;
	protected static $_connection;

	public static function getConnection()
	{
		if (self::$_connection == null) {
			self::$_connection = new \PDO('sqlite:' . \Registry::get('root') . '/database/orders.db');
			self::$_connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
			self::$_connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION); // ERRMODE_WARNING | ERRMODE_EXCEPTION | ERRMODE_SILENT
		}

		return self::$_connection;
	}

	public function get(array $criterias = array())
	{
		$sql = "SELECT * FROM "
			. static::$_table
			. (!empty($criterias) ? " WHERE " : "");

		$where = array();
		foreach ($criterias as $name => $value) {
			$where[] = $name . " = :" . $name;
		}

		$sql .= implode(" AND ", $where);

		$stmt = self::getConnection()->prepare($sql);
		$stmt->execute($criterias);
		return $stmt->fetchAll();
	}
}
