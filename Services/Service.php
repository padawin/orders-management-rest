<?php

namespace Services;

abstract class Service
{
	public function get(array $criterias = array())
	{
		throw new \Exceptions\MethodNotAllowed(
			"Get method is not allowed for this service"
		);
	}

	public function put(array $values = array(), array $conditions = array())
	{
		throw new \Exceptions\MethodNotAllowed(
			"Put method is not allowed for this service"
		);
	}

	public function post(array $values = array())
	{
		throw new \Exceptions\MethodNotAllowed(
			"Post method is not allowed for this service"
		);
	}

	public function delete(array $conditions = array())
	{
		throw new \Exceptions\MethodNotAllowed(
			"Delete method is not allowed for this service"
		);
	}
}
