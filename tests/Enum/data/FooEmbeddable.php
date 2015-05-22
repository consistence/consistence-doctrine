<?php

namespace Consistence\Doctrine\Enum;

use Consistence\Doctrine\Enum\EnumAnnotation as Enum;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class FooEmbeddable
{

	/**
	 * @Enum(class=FooEnum::class)
	 * @ORM\Column(type="integer_enum")
	 * @var \Consistence\Doctrine\Enum\FooEnum
	 */
	private $enum = FooEnum::ONE;

	/**
	 * @ORM\Embedded(class=BarEmbeddable::class)
	 * @var \Consistence\Doctrine\Enum\BarEmbeddable
	 */
	private $embedded;

	public function __construct()
	{
		$this->embedded = new BarEmbeddable();
	}

	/**
	 * @return \Consistence\Doctrine\Enum\FooEnum
	 */
	public function getEnum()
	{
		return $this->enum;
	}

	/**
	 * @return \Consistence\Doctrine\Enum\BarEmbeddable
	 */
	public function getEmbedded()
	{
		return $this->embedded;
	}

}
