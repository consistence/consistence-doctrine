<?php

namespace Consistence\Doctrine\Enum;

use Consistence\Enum\Enum;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;

use ReflectionClass;

class EnumPostLoadEntityListener
{

	const EMBEDDED_SEPARATOR = '.';

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
		$fieldName
	)
	{
		$metadata = $this->getClassMetadata($entityManager, get_class($entity));

		list($object, $classReflection, $propertyName) = $this->resolveObjectAndProperty(
			$entityManager,
			$entity,
			$fieldName
		);
		$property = $this->getProperty($classReflection, $propertyName);
		$annotation = $this->annotationReader->getPropertyAnnotation($property, EnumAnnotation::class);
		if ($annotation !== null) {
			$property->setAccessible(true);
			$scalarValue = $property->getValue($object);
			if (is_scalar($scalarValue)) {
				$enumClass = $metadata->fullyQualifiedClassName($annotation->class);
				if (!is_a($enumClass, Enum::class, true)) {
					throw new \Consistence\Doctrine\Enum\NotEnumException($enumClass);
				}
				$enum = $enumClass::get($scalarValue);
				$property->setValue($object, $enum);
				$entityManager->getUnitOfWork()->setOriginalEntityProperty(
					spl_object_hash($entity),
					$fieldName,
					$enum
				);
			}
		}
	}

	/**
	 * @param \ReflectionClass $classReflection
	 * @param string $propertyName
	 * @return \ReflectionProperty
	 */
	private function getProperty(ReflectionClass $classReflection, $propertyName)
	{
		try {
			return $classReflection->getProperty($propertyName);
		} catch (\ReflectionException $e) {
			$parentClass = $classReflection->getParentClass();
			if ($parentClass === false) {
				throw $e;
			}
			return $this->getProperty($parentClass, $propertyName);
		}
	}

	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 * @param object $object
	 * @param string $fieldName
	 * @return mixed[]
	 */
	private function resolveObjectAndProperty(
		EntityManager $entityManager,
		$object,
		$fieldName
	)
	{
		$metadata = $this->getClassMetadata($entityManager, get_class($object));

		$parts = explode(self::EMBEDDED_SEPARATOR, $fieldName);
		if (count($parts) === 1) {
			return [
				$object,
				$metadata->getReflectionClass(),
				$fieldName
			];
		}

		$propertyName = array_shift($parts);
		$property = $metadata->getReflectionClass()->getProperty($propertyName);
		$property->setAccessible(true);

		return $this->resolveObjectAndProperty(
			$entityManager,
			$property->getValue($object),
			implode(self::EMBEDDED_SEPARATOR, $parts)
		);
	}

	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 * @param string $class
	 * @return \Doctrine\ORM\Mapping\ClassMetadata
	 */
	private function getClassMetadata(EntityManager $entityManager, $class)
	{
		$metadata = $entityManager->getClassMetadata($class);
		if (!($metadata instanceof ClassMetadata)) {
			throw new \Consistence\Doctrine\Enum\UnsupportedClassMetadataException(get_class($metadata));
		}

		return $metadata;
	}

}
