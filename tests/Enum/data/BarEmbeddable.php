<?php

namespace Consistence\Doctrine\Enum;

use Consistence\Doctrine\Enum\EnumAnnotation as Enum;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class BarEmbeddable
{

	/**
	 * @Enum(class=FooEnum::class)
	 * @ORM\Column(type="integer_enum")
	 * @var \Consistence\Doctrine\Enum\FooEnum
	 */
	private $enum = FooEnum::ONE;

	/**
	 * @return \Consistence\Doctrine\Enum\FooEnum
	 */
	public function getEnum()
	{
		return $this->enum;
	}

}
