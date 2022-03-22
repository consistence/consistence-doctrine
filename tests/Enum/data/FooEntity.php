<?php

declare(strict_types = 1);

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

	/**
	 * This simulates for example using partials where this was not selected
	 *
	 * @ORM\Embedded(class=FooEmbeddable::class, columnPrefix="not_loaded_")
	 * @var \Consistence\Doctrine\Enum\FooEmbeddable
	 */
	private $notLoadedEmbedded;

	public function __construct()
	{
		$this->embedded = new FooEmbeddable();
	}

	public function setEnum(FooEnum $enum): void
	{
		$this->enum = $enum;
	}

	public function getEnum(): FooEnum
	{
		return $this->enum;
	}

	public function getNullableEnum(): ?FooEnum
	{
		return $this->nullableEnum;
	}

	public function getWithoutNamespace(): FooEnum
	{
		return $this->withoutNamespace;
	}

	public function getEmbedded(): FooEmbeddable
	{
		return $this->embedded;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
	 *
	 * @return \Consistence\Doctrine\Enum\FooEmbeddable
	 */
	public function getNotLoadedEmbedded()
	{
		return $this->notLoadedEmbedded;
	}

}
