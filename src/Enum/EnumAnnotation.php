<?php

namespace Consistence\Doctrine\Enum;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
class EnumAnnotation
{

	/**
	 * @Required
	 * @var string
	 */
	public $class;

}
