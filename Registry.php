<?php

class Registry
{
	protected static $_data = array();
	public function get($key)
	{
		if (!isset(self::$_data[$key])) {
			throw new Exception("undefined key $key in the registry");
		}

		return self::$_data[$key];
	}

	public function set($key, $value)
	{
		self::$_data[$key] = $value;
	}
}
