<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum\Type;

use Consistence\Enum\Enum;

class EnumType
{

	/**
	 * @param \Consistence\Enum\Enum|null $value
	 * @return mixed
	 */
	public static function convertToDatabaseValue(?Enum $value)
	{
		if ($value === null) {
			return null;
		}

		return $value->getValue();
	}

}
