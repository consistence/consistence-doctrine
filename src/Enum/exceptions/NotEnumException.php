<?php

namespace Consistence\Doctrine\Enum;

class NotEnumException extends \Consistence\PhpException implements \Consistence\Doctrine\Enum\Exception
{

	/** @var string */
	private $class;

	/**
	 * @param string $enumClass
	 * @param \Exception|null $previous
	 */
	public function __construct($enumClass, \Exception $previous = null)
	{
		parent::__construct(sprintf('Class %s is not an Enum', $enumClass), $previous);
		$this->class = $enumClass;
	}

	/**
	 * @return string
	 */
	public function getEnumClass()
	{
		return $this->class;
	}

}
