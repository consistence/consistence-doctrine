<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\Mapping\ClassMetadata as CommonClassMetadata;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;

use ReflectionClass;

class EnumPostLoadEntityListenerTest extends \PHPUnit\Framework\TestCase
{

	public function testLoadWrongClassMetadata()
	{
		$reader = $this->createMock(Reader::class);

		$postLoadListener = new EnumPostLoadEntityListener($reader);

		$classMetadata = $this->createMock(CommonClassMetadata::class);

		$entityManager = $this->createMock(EntityManager::class);
		$entityManager
			->expects($this->once())
			->method('getClassMetadata')
			->will($this->returnValue($classMetadata));

		$loadEvent = new LifecycleEventArgs(new FooEntity(), $entityManager);

		try {
			$postLoadListener->postLoad($loadEvent);
		} catch (\Consistence\Doctrine\Enum\UnsupportedClassMetadataException $e) {
			$this->assertSame(get_class($classMetadata), $e->getGivenClassMetadataClass());
		}
	}

	public function testLoadMissingProperty()
	{
		$reader = $this->createMock(Reader::class);

		$postLoadListener = new EnumPostLoadEntityListener($reader);

		$fooEntity = new FooEntity();
		$fooEntityReflection = new ReflectionClass($fooEntity);

		$classMetadata = $this->createMock(ClassMetadata::class);
		$classMetadata
			->expects($this->once())
			->method('getFieldNames')
			->will($this->returnValue(['nonExistingProperty']));
		$classMetadata
			->expects($this->once())
			->method('getReflectionClass')
			->will($this->returnValue($fooEntityReflection));

		$entityManager = $this->createMock(EntityManager::class);
		$entityManager
			->method('getClassMetadata')
			->will($this->returnValue($classMetadata));

		$this->expectException(\ReflectionException::class);
		$this->expectExceptionMessage('nonExistingProperty');

		$loadEvent = new LifecycleEventArgs($fooEntity, $entityManager);
		$postLoadListener->postLoad($loadEvent);
	}

}
