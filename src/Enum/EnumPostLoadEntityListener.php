<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum;

use Consistence\Enum\Enum;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;

class EnumPostLoadEntityListener
{

	/** @var \Doctrine\Common\Annotations\Reader */
	private $annotationReader;

	public function __construct(Reader $annotationReader)
	{
		$this->annotationReader = $annotationReader;
	}

	public function postLoad(LifecycleEventArgs $event)
	{
		$entity = $event->getEntity();
		$entityManager = $event->getEntityManager();
		$metadata = $this->getClassMetadata($entityManager, get_class($entity));
		foreach ($metadata->getFieldNames() as $fieldName) {
			$this->processField($entityManager, $entity, $fieldName);
		}
	}

	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 * @param object $entity
	 * @param string $fieldName
	 */
	private function processField(
		EntityManager $entityManager,
		$entity,
		string $fieldName
	)
	{
		$metadata = $this->getClassMetadata($entityManager, get_class($entity));

		$annotation = $this->annotationReader->getPropertyAnnotation($metadata->getReflectionProperty($fieldName), EnumAnnotation::class);

		if ($annotation !== null) {
			$scalarValue = $metadata->getFieldValue($entity, $fieldName);
			if (is_scalar($scalarValue)) {
				$enumClass = $metadata->fullyQualifiedClassName($annotation->class);
				if (!is_a($enumClass, Enum::class, true)) {
					throw new \Consistence\Doctrine\Enum\NotEnumException($enumClass);
				}
				$enum = $enumClass::get($scalarValue);
				$metadata->setFieldValue($entity, $fieldName, $enum);
				$entityManager->getUnitOfWork()->setOriginalEntityProperty(
					spl_object_hash($entity),
					$fieldName,
					$enum
				);
			}
		}
	}

	private function getClassMetadata(
		EntityManager $entityManager,
		string $class
	): ClassMetadata
	{
		$metadata = $entityManager->getClassMetadata($class);
		if (!($metadata instanceof ClassMetadata)) {
			throw new \Consistence\Doctrine\Enum\UnsupportedClassMetadataException(get_class($metadata));
		}

		return $metadata;
	}

}
