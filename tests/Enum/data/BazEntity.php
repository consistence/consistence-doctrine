<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum;

use Consistence\Doctrine\Enum\EnumAnnotation as Enum;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class BazEntity extends \Consistence\Doctrine\Enum\FooParentEntity
{

	/**
	 * @Enum(class="NonExistingClass")
	 * @ORM\Column(type="integer_enum")
	 * @var \Consistence\Doctrine\Enum\FooEnum|null
	 */
	private $nonExistingEnumClass = FooEnum::ONE;

}
