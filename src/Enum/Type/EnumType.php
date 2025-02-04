<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum\Type;

use Consistence\Enum\Enum;

class EnumType extends \Consistence\ObjectPrototype
{

	/** @var string */
	private $enumClass;

	public function __construct(string $enumClass)
	{
		if (!is_a($enumClass, Enum::class, true)) {
			throw new \Consistence\Doctrine\Enum\Type\CannotCreateEnumTypeWithClassWhichIsNotEnumException($enumClass);
		}

		if ($enumClass[0] === '\\') {
			$enumClass = substr($enumClass, 1);
		}

		$this->enumClass = $enumClass;
	}

	/**
	 * @param \Consistence\Enum\Enum|null $value
	 * @return mixed
	 */
	public function convertToDatabaseValue(?Enum $value)
	{
		if ($value === null) {
			return null;
		}

		return $value->getValue();
	}

	/**
	 * @param mixed $value
	 * @return mixed
	 */
	public function convertToPhpValue(
		$value
	): ?Enum
	{
		if ($value === null) {
			return null;
		}

		return $this->enumClass::get($value);
	}

	public static function formatName(string $enumClass): string
	{
		if (!is_a($enumClass, Enum::class, true)) {
			throw new \Consistence\Doctrine\Enum\Type\CannotFormatEnumTypeNameWithClassWhichIsNotEnumException($enumClass);
		}

		return sprintf('enum<%s>', $enumClass);
	}

	public function getName(): string
	{
		return self::formatName($this->enumClass);
	}

}
