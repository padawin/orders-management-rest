<?php

namespace models\Order;

require_once "models/Sqlite.php";

class Sqlite extends \models\Sqlite
{
	protected static $_table = 'orders';
}
