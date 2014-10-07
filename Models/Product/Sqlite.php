<?php

namespace Models\Product;

class Sqlite extends \Models\Sqlite
{
	protected static $_table = 'product';
	protected static $_updatableFields = array('name', 'price');
}
