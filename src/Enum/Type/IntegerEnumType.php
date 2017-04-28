<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;

class IntegerEnumType extends \Doctrine\DBAL\Types\IntegerType
{

	const NAME = 'integer_enum';

	public function getName(): string
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

	public function requiresSQLCommentHint(AbstractPlatform $platform): bool
	{
		return true;
	}

}
