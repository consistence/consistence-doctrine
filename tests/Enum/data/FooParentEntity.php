<?php

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
	 */
	private $id;

}
