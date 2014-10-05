<?php

namespace services;

abstract class Service
{
	public function get(array $criterias = array())
	{
		throw new \Exception("Invalid method");
	}

	public function put(array $values = array(), array $conditions = array())
	{
		throw new \Exception("Invalid method");
	}

	public function post(array $values = array())
	{
		throw new \Exception("Invalid method");
	}

	public function delete($id)
	{
		throw new \Exception("Invalid method");
	}
}
