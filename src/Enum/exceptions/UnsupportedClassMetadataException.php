<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum;

use Doctrine\ORM\Mapping\ClassMetadata;

class UnsupportedClassMetadataException extends \Consistence\PhpException
{

	/** @var string */
	private $givenClassMetadataClass;

	public function __construct(string $givenClassMetadataClass, \Throwable $previous = null)
	{
		parent::__construct(sprintf(
			'Instance of %s expected, %s given',
			ClassMetadata::class,
			$givenClassMetadataClass
		), $previous);
		$this->givenClassMetadataClass = $givenClassMetadataClass;
	}

	public function getGivenClassMetadataClass(): string
	{
		return $this->givenClassMetadataClass;
	}

}
