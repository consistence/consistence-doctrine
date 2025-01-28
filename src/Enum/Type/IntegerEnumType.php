<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;

class IntegerEnumType extends \Doctrine\DBAL\Types\IntegerType
{

	public const NAME = 'integer_enum';

	public function getName(): string
	{
		return self::NAME;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 *
	 * @param \Consistence\Enum\Enum|null $value
	 * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
	 * @return int|null
	 */
	public function convertToDatabaseValue($value, AbstractPlatform $platform): ?int
	{
		return EnumType::convertToDatabaseValue($value);
	}

	public function requiresSQLCommentHint(AbstractPlatform $platform): bool
	{
		return true;
	}

}
