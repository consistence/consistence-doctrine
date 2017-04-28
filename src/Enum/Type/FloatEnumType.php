<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;

class FloatEnumType extends \Doctrine\DBAL\Types\FloatType
{

	const NAME = 'float_enum';

	public function getName(): string
	{
		return self::NAME;
	}

	/**
	 * @param \Consistence\Enum\Enum|float|null $value
	 * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
	 * @return float|null
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
