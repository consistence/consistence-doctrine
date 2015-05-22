<?php

namespace Consistence\Doctrine\Enum;

use Consistence\Doctrine\Enum\EnumAnnotation as Enum;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class FooEntity extends \Consistence\Doctrine\Enum\FooParentEntity
{

	/**
	 * @Enum(class=FooEnum::class)
	 * @ORM\Column(type="integer_enum")
	 * @var \Consistence\Doctrine\Enum\FooEnum
	 */
	private $enum = FooEnum::ONE;

	/**
	 * @Enum(class=FooEnum::class)
	 * @ORM\Column(type="integer_enum")
	 * @var \Consistence\Doctrine\Enum\FooEnum|null
	 */
	private $nullableEnum;

	/**
	 * @Enum(class="FooEnum")
	 * @ORM\Column(type="integer_enum")
	 * @var \Consistence\Doctrine\Enum\FooEnum
	 */
	private $withoutNamespace = FooEnum::ONE;

	/**
	 * @ORM\Embedded(class=FooEmbeddable::class)
	 * @var \Consistence\Doctrine\Enum\FooEmbeddable
	 */
	private $embedded;

	public function __construct()
	{
		$this->embedded = new FooEmbeddable();
	}

	/**
	 * @return \Consistence\Doctrine\Enum\FooEnum
	 */
	public function getEnum()
	{
		return $this->enum;
	}

	/**
	 * @return \Consistence\Doctrine\Enum\FooEnum|null
	 */
	public function getNullableEnum()
	{
		return $this->nullableEnum;
	}

	/**
	 * @return \Consistence\Doctrine\Enum\FooEnum
	 */
	public function getWithoutNamespace()
	{
		return $this->withoutNamespace;
	}

	/**
	 * @return \Consistence\Doctrine\Enum\FooEmbeddable
	 */
	public function getEmbedded()
	{
		return $this->embedded;
	}

}
