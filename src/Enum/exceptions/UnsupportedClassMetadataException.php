<?php

namespace Consistence\Doctrine\Enum;

use Doctrine\ORM\Mapping\ClassMetadata;

class UnsupportedClassMetadataException extends \Consistence\PhpException implements \Consistence\Doctrine\Enum\Exception
{

	/** @var string */
	private $givenClassMetadataClass;

	/**
	 * @param string $givenClassMetadataClass
	 * @param \Exception|null $previous
	 */
	public function __construct($givenClassMetadataClass, \Exception $previous = null)
	{
		parent::__construct(sprintf(
			'Instance of %s expected, %s given',
			ClassMetadata::class,
			$givenClassMetadataClass
		), $previous);
		$this->givenClassMetadataClass = $givenClassMetadataClass;
	}

	/**
	 * @return string
	 */
	public function getGivenClassMetadataClass()
	{
		return $this->givenClassMetadataClass;
	}

}
