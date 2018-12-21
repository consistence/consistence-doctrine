<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Persistence\Mapping\ClassMetadata as CommonClassMetadata;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;

class EnumPostLoadEntityListenerTest extends \PHPUnit\Framework\TestCase
{

	public function testLoadWrongClassMetadata(): void
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

	public function testWarmupCache(): void
	{
		$reader = $this->createMock(Reader::class);
		$cache = new ArrayCache();

		$postLoadListener = new EnumPostLoadEntityListener($reader, $cache);

		$classMetadata = new ClassMetadata(FooEntity::class);

		$entityManager = $this->createMock(EntityManager::class);
		$entityManager
			->expects($this->once())
			->method('getClassMetadata')
			->will($this->returnValue($classMetadata));

		$postLoadListener->warmUpCache($entityManager, FooEntity::class);

		$this->assertTrue($cache->contains(FooEntity::class));
	}

}
