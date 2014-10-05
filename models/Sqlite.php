<?php

namespace models;

require_once "models/Model.php";
require_once "Registry.php";
require_once "exceptions/BadRequest.php";
require_once "exceptions/Duplicate.php";

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

		$stmt = $this->_execute($sql, $criterias)[0];
		return $stmt->fetchAll();
	}

	public function update(array $values, array $criterias = array())
	{
		$sql = "UPDATE " . static::$_table . " SET ";
		$params = $fields = $where = array();
		foreach ($values as $col => $val) {
			$fields[] = $col . ' = :col_' . $col;
			$params['col_' . $col] = $val;
		}
		foreach ($criterias as $col => $val) {
			$where[] = $col . ' = :where_' . $col;
			$params['where_' . $col] = $val;
		}

		$sql .= implode(', ', $fields)
		. (!empty($where) ? " WHERE " . implode(" AND ", $where) : '');

		return $this->_execute($sql, $params)[1];
	}

	public function insert(array $values)
	{
		if (empty($values)) {
			throw new \exceptions\BadRequest;
		}

		$sql = sprintf(
			"INSERT INTO " . static::$_table . " (%s) VALUES (%s)",
			implode(', ', array_keys($values)),
			implode(', ', array_fill(0, count($values), '?'))
		);

		return $this->_execute($sql, array_values($values))[1];
	}

	protected function _execute($sql, $params = array())
	{
		try {
			$stmt = self::getConnection()->prepare($sql);
			$result = $stmt->execute($params);
		}
		catch (\Exception $e) {
			switch ($e->getCode()) {
				case 23000:
					throw new \exceptions\Duplicate;
				case 'HY000':
					throw new \exceptions\BadRequest;
				default:
					throw $e;
			}
		}
		return array($stmt, $result);
	}
}
