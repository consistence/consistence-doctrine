<?php

namespace Consistence\Doctrine\Enum\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;

class StringEnumType extends \Doctrine\DBAL\Types\StringType
{

	const NAME = 'string_enum';

	/**
	 * @return string
	 */
	public function getName()
	{
		return self::NAME;
	}

	/**
	 * @param \Consistence\Enum\Enum|string|null $value
	 * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
	 * @return string|null
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
