<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum\Type;

use Consistence\Enum\Enum;

class CannotCreateEnumTypeWithClassWhichIsNotEnumException extends \Consistence\PhpException
{

	/** @var string */
	private $enumClass;

	public function __construct(string $enumClass, ?\Throwable $previous = null)
	{
		parent::__construct(sprintf(
			'Cannot create enum type with class %s which is not of %s type',
			$enumClass,
			Enum::class
		), $previous);
		$this->enumClass = $enumClass;
	}

	public function getEnumClass(): string
	{
		return $this->enumClass;
	}

}
