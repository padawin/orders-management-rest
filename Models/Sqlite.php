<?php

namespace Models;

use \Registry;
use \PDO;
use \InvalidArgumentException;
use \Exceptions\BadRequest;
use \Exceptions\Duplicate;
use \Exception;

/**
 * Abstract class to interact with a Sqlite database
 */
abstract class Sqlite implements Model
{
	/**
	 * The table of the child class
	 */
	protected static $_table;

	/**
	 * A PDO connection
	 */
	protected static $_connection;

	/**
	 * The list of fields which can be updated
	 */
	protected static $_updatableFields = array();

	/**
	 * Returns a PDO connection
	 * Singleton.
	 *
	 * @return PDO
	 */
	public static function getConnection()
	{
		if (self::$_connection == null) {
			self::$_connection = new PDO('sqlite:' . Registry::get('db'));
			self::$_connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
			self::$_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // ERRMODE_WARNING | ERRMODE_EXCEPTION | ERRMODE_SILENT
		}

		return self::$_connection;
	}

	/**
	 * Method to fetch rows from the database
	 *
	 * @param array $criterias (Optional) The criterias to fetch the data
	 * @return array The data matching the criterias
	 */
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


	/**
	 * Method to update rows in the database from some criterias
	 *
	 * @param array $values The new values to set
	 * @param array $criterias (Optional) The criterias to update the data
	 * @return integer The number of updated rows
	 */
	public function update(array $values, array $criterias = array())
	{
		$values = array_intersect_key(
			$values,
			array_flip(static::$_updatableFields)
		);
		if (empty($values)) {
			throw new InvalidArgumentException("No value to update");
		}

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

		$stmt = $this->_execute($sql, $params)[0];
		return $stmt->rowCount();
	}

	/**
	 * Insert a new row in the database
	 *
	 * @param array $values the row to insert
	 * @return integer The id of the inserted row
	 */
	public function insert(array $values)
	{
		if (empty($values)) {
			throw new BadRequest;
		}

		$sql = sprintf(
			"INSERT INTO " . static::$_table . " (%s) VALUES (%s)",
			implode(', ', array_keys($values)),
			implode(', ', array_fill(0, count($values), '?'))
		);

		$stmt = $this->_execute($sql, array_values($values))[0];
		return array(self::getConnection()->lastInsertId());
	}

	/**
	 * Delete rows from the database.
	 *
	 * @param array $conditions The rows to delete must match those conditions
	 * @return integer The number of deleted rows
	 */
	public function delete(array $conditions)
	{
		$sql = "DELETE FROM " . static::$_table;

		foreach ($conditions as $col => $val) {
			$where[] = $col . ' = :' . $col;
		}

		$sql .= (!empty($where) ? " WHERE " . implode(" AND ", $where) : '');

		$stmt = $this->_execute($sql, $conditions)[0];
		return $stmt->rowCount();
	}

	/**
	 * Execute a SQL query
	 *
	 * @param string $sql
	 * @param array $params Parameters to bind
	 * @return a tuple with the executed statement and the result of the
	 *		statement's execution.
	 */
	protected function _execute($sql, array $params = array())
	{
		try {
			$stmt = self::getConnection()->prepare($sql);
			$result = $stmt->execute($params);
		}
		catch (Exception $e) {
			switch ((string) $e->getCode()) {
				case '23000':
					throw new Duplicate($e->getMessage());
				case 'HY000':
					throw new BadRequest($e->getMessage());
				default:
					throw $e;
			}
		}
		return array($stmt, $result);
	}
}
