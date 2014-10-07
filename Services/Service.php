<?php

namespace Services;

use \Exceptions\MethodNotAllowed;

abstract class Service
{
	public function get(array $criterias = array())
	{
		throw new MethodNotAllowed(
			"Get method is not allowed for this service"
		);
	}

	public function put(array $values = array(), array $conditions = array())
	{
		throw new MethodNotAllowed(
			"Put method is not allowed for this service"
		);
	}

	public function post(array $values = array())
	{
		throw new MethodNotAllowed(
			"Post method is not allowed for this service"
		);
	}

	public function delete(array $conditions = array())
	{
		throw new MethodNotAllowed(
			"Delete method is not allowed for this service"
		);
	}
}
