<?php

namespace Consistence\Doctrine\Enum\Type;

use Consistence\Enum\Enum;

class EnumType
{

	/**
	 * @param \Consistence\Enum\Enum|mixed $value
	 * @return mixed
	 */
	public static function convertToDatabaseValue($value)
	{
		if ($value instanceof Enum) {
			return $value->getValue();
		}

		return $value;
	}

}
