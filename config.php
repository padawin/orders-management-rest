<?php

$config = array(
	'root' => '/path/to/the/project/root',
	'default-vat' => .2,
	'db' => '/path/to/the/project/database.db'
);

foreach ($config as $key => $value) {
	Registry::set($key, $value);
}

unset($config);
