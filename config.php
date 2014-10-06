<?php

require_once "Registry.php";

$config = array(
	'root' => '/home/ghislain/projets/workspace/php/order-management',
	'default-vat' => .2
);

foreach ($config as $key => $value) {
	Registry::set($key, $value);
}

unset($config);
