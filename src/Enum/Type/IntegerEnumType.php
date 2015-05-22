<?php

namespace Consistence\Doctrine\Enum\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;

class IntegerEnumType extends \Doctrine\DBAL\Types\IntegerType
{

	const NAME = 'integer_enum';

	/**
	 * @return string
	 */
	public function getName()
	{
		return self::NAME;
	}

	/**
	 * @param \Consistence\Enum\Enum|integer|null $value
	 * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
	 * @return integer|null
	 */
	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		return EnumType::convertToDatabaseValue($value);
	}

	/**
	 * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
	 * @return boolean
	 */
	public function requiresSQLCommentHint(AbstractPlatform $platform)
	{
		return true;
	}

}
