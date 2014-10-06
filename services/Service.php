<?php

namespace services;

require "exceptions/MethodNotAllowed.php";

abstract class Service
{
	public function get(array $criterias = array())
	{
		throw new \exceptions\MethodNotAllowed(
			"Get method is not allowed for this service"
		);
	}

	public function put(array $values = array(), array $conditions = array())
	{
		throw new \exceptions\MethodNotAllowed(
			"Put method is not allowed for this service"
		);
	}

	public function post(array $values = array())
	{
		throw new \exceptions\MethodNotAllowed(
			"Post method is not allowed for this service"
		);
	}

	public function delete($id)
	{
		throw new \exceptions\MethodNotAllowed(
			"Delete method is not allowed for this service"
		);
	}
}
