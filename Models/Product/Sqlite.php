<?php

namespace Models\Product;

require_once "Models/Sqlite.php";

class Sqlite extends \Models\Sqlite
{
	protected static $_table = 'product';
	protected static $_updatableFields = array('name', 'price');
}
