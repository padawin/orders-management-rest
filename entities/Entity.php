<?php

namespace Entities;

/**
 * Abstract class for the entities. An entity class has a model, to interact
 * with a storage (Sql or NoSql database, memory....)
 */
abstract class Entity
{
	/**
	 * Set the entity's model
	 *
	 * @param \Models\Model $model
	 */
	public static function setModel(\Models\Model $model)
	{
		static::$_model = $model;
	}

	/**
	 * Get the entity's model
	 *
	 * @throws RuntimeException if no model is set. A model must be set to use
	 * the entity
	 */
	public static function getModel()
	{
		if (static::$_model == null) {
			throw new \RuntimeException("No model set");
		}

		return static::$_model;
	}
}
