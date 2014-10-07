<?php

$config = array(
	'root' => '/path/to/the/project/root',
	'default-vat' => .2
);

foreach ($config as $key => $value) {
	Registry::set($key, $value);
}

unset($config);
