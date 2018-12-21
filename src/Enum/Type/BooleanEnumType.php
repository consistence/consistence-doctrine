<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;

class BooleanEnumType extends \Doctrine\DBAL\Types\BooleanType
{

	public const NAME = 'boolean_enum';

	public function getName(): string
	{
		return self::NAME;
	}

	/**
	 * @param \Consistence\Enum\Enum|bool|null $value
	 * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
	 * @return bool|null
	 */
	public function convertToDatabaseValue($value, AbstractPlatform $platform): ?bool
	{
		return EnumType::convertToDatabaseValue($value);
	}

	public function requiresSQLCommentHint(AbstractPlatform $platform): bool
	{
		return true;
	}

}
