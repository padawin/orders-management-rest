<?php

namespace models\Product;

require_once "models/Sqlite.php";

class Sqlite extends \models\Sqlite
{
	protected static $_table = 'product';
}
