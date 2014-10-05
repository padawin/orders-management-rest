<?php

namespace services;

require_once "Service.php";

class order extends Service
{
	public function get(array $criterias = array())
	{
		var_dump($criterias);
	}
}
