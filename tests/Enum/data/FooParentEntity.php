<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperClass
 */
class FooParentEntity
{

	/**
	 * @ORM\Id()
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $id;

}
