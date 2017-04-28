<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum;

use Consistence\Doctrine\Enum\EnumAnnotation as Enum;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class BaxEntity extends \Consistence\Doctrine\Enum\FooParentEntity
{

	/**
	 * @Enum(class="FooEntity")
	 * @ORM\Column(type="integer_enum")
	 * @var \Consistence\Doctrine\Enum\FooEnum|null
	 */
	private $notEnumClass = FooEnum::ONE;

}
