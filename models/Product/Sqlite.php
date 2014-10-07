<?php

namespace Models\Product;

require_once "models/Sqlite.php";

class Sqlite extends \Models\Sqlite
{
	protected static $_table = 'product';
	protected static $_updatableFields = array('name', 'price');
}
