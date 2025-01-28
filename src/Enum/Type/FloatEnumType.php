<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;

class FloatEnumType extends \Doctrine\DBAL\Types\FloatType
{

	public const NAME = 'float_enum';

	public function getName(): string
	{
		return self::NAME;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 *
	 * @param \Consistence\Enum\Enum|null $value
	 * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
	 * @return float|null
	 */
	public function convertToDatabaseValue($value, AbstractPlatform $platform): ?float
	{
		return EnumType::convertToDatabaseValue($value);
	}

	public function requiresSQLCommentHint(AbstractPlatform $platform): bool
	{
		return true;
	}

}
