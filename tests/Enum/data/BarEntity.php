<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum;

use Consistence\Doctrine\Enum\EnumAnnotation as Enum;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class BarEntity extends \Consistence\Doctrine\Enum\FooParentEntity
{

	/**
	 * @Enum()
	 * @var \Consistence\Doctrine\Enum\FooEnum|null
	 */
	private $missingEnumClass;

}
