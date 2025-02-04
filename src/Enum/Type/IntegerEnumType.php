<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum\Type;

use Consistence\Enum\Enum;
use Consistence\Type\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class IntegerEnumType extends \Doctrine\DBAL\Types\IntegerType
{

	/** @var \Consistence\Doctrine\Enum\Type\EnumType|null */
	private $enumType;

	public static function create(string $enumClass): self
	{
		$type = new self();
		$type->enumType = new EnumType($enumClass);

		return $type;
	}

	public function getName(): string
	{
		return $this->enumType->getName();
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 *
	 * @param \Consistence\Enum\Enum|null $value
	 * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
	 * @return bool|null
	 */
	public function convertToDatabaseValue($value, AbstractPlatform $platform): ?int
	{
		return $this->getEnumType()->convertToDatabaseValue($value);
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 *
	 * @param mixed $value
	 * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
	 * @return \Consistence\Enum\Enum|null
	 */
	public function convertToPHPValue($value, AbstractPlatform $platform): ?Enum
	{
		$value = parent::convertToPHPValue($value, $platform);

		Type::checkType($value, 'int|null');

		return $this->getEnumType()->convertToPhpValue($value);
	}

	public function requiresSQLCommentHint(AbstractPlatform $platform): bool
	{
		return true;
	}

	private function getEnumType(): EnumType
	{
		if ($this->enumType === null) {
			throw new \Consistence\Doctrine\Enum\Type\CannotUseEnumTypeWithoutEnumClassException(self::class);
		}

		return $this->enumType;
	}

}
