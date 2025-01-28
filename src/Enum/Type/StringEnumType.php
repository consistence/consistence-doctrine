<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;

class StringEnumType extends \Doctrine\DBAL\Types\StringType
{

	public const NAME = 'string_enum';

	public function getName(): string
	{
		return self::NAME;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 *
	 * @param \Consistence\Enum\Enum|null $value
	 * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
	 * @return string|null
	 */
	public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
	{
		return EnumType::convertToDatabaseValue($value);
	}

	public function requiresSQLCommentHint(AbstractPlatform $platform): bool
	{
		return true;
	}

}
