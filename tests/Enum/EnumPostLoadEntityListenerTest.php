<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\ClassMetadata as PersistenceClassMetadata;
use PHPUnit\Framework\Assert;

class EnumPostLoadEntityListenerTest extends \PHPUnit\Framework\TestCase
{

	public function testLoadWrongClassMetadata(): void
	{
		$reader = $this->createMock(Reader::class);

		$postLoadListener = new EnumPostLoadEntityListener($reader);

		$classMetadata = $this->createMock(PersistenceClassMetadata::class);

		$entityManager = $this->createMock(EntityManager::class);
		$entityManager
			->expects(self::once())
			->method('getClassMetadata')
			->will(self::returnValue($classMetadata));

		$loadEvent = new LifecycleEventArgs(new FooEntity(), $entityManager);

		try {
			$postLoadListener->postLoad($loadEvent);
			Assert::fail('Exception expected');
		} catch (\Consistence\Doctrine\Enum\UnsupportedClassMetadataException $e) {
			Assert::assertSame(get_class($classMetadata), $e->getGivenClassMetadataClass());
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
			->expects(self::once())
			->method('getClassMetadata')
			->will(self::returnValue($classMetadata));

		$postLoadListener->warmUpCache($entityManager, FooEntity::class);

		Assert::assertTrue($cache->contains(FooEntity::class));
	}

}
