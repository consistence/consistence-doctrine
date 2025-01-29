<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum\Type;

class CannotUseEnumTypeWithoutEnumClassException extends \Consistence\PhpException
{

	/** @var string */
	private $dbalTypeClass;

	public function __construct(string $dbalTypeClass, ?\Throwable $previous = null)
	{
		parent::__construct(sprintf(
			'%s needs to know with which enum class is this type meant to be used.'
				. 'Create instance of this DBAL type with %s::create() and pass the class as argument.',
			self::class,
			self::class
		), $previous);
		$this->dbalTypeClass = $dbalTypeClass;
	}

	public function getDbalTypeClass(): string
	{
		return $this->dbalTypeClass;
	}

}
