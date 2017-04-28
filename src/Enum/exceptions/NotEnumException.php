<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum;

class NotEnumException extends \Consistence\PhpException
{

	/** @var string */
	private $class;

	public function __construct(string $enumClass, \Throwable $previous = null)
	{
		parent::__construct(sprintf('Class %s is not an Enum', $enumClass), $previous);
		$this->class = $enumClass;
	}

	public function getEnumClass(): string
	{
		return $this->class;
	}

}
