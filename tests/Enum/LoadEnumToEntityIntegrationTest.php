<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Tools\Setup;
use Generator;
use PHPUnit\Framework\Assert;

class LoadEnumToEntityIntegrationTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @return mixed[][]|\Generator
	 */
	public function loadEnumToEntityDataProvider(): Generator
	{
		yield 'entity' => [
			'foo' => new FooEntity(),
		];

		yield 'unserialized entity' => (function (): array {
			$fooBeforeSerialization = new FooEntity();
			$fooBeforeSerialization->setEnum(FooEnum::get(FooEnum::ONE));

			return [
				'foo' => unserialize(serialize($fooBeforeSerialization)),
			];
		})();
	}

	/**
	 * @dataProvider loadEnumToEntityDataProvider
	 *
	 * @param \Consistence\Doctrine\Enum\FooEntity $foo
	 */
	public function testLoadEnumToEntity(FooEntity $foo): void
	{
		$this->callPostLoadEventOnEntity($foo);

		Assert::assertSame(FooEnum::get(FooEnum::ONE), $foo->getEnum());
		Assert::assertSame(FooEnum::get(FooEnum::ONE), $foo->getWithoutNamespace());
		Assert::assertSame(FooEnum::get(FooEnum::ONE), $foo->getEmbedded()->getEnum());
		Assert::assertSame(FooEnum::get(FooEnum::ONE), $foo->getEmbedded()->getEmbedded()->getEnum());
		Assert::assertNull($foo->getNullableEnum());
		Assert::assertNull($foo->getNotLoadedEmbedded());
	}

	/**
	 * @dataProvider loadEnumToEntityDataProvider
	 *
	 * @param \Consistence\Doctrine\Enum\FooEntity $foo
	 */
	public function testMultipleLoadEvents(FooEntity $foo): void
	{
		$this->callPostLoadEventOnEntity($foo);
		$this->callPostLoadEventOnEntity($foo);

		Assert::assertSame(FooEnum::get(FooEnum::ONE), $foo->getEnum());
		Assert::assertSame(FooEnum::get(FooEnum::ONE), $foo->getWithoutNamespace());
		Assert::assertSame(FooEnum::get(FooEnum::ONE), $foo->getEmbedded()->getEnum());
		Assert::assertSame(FooEnum::get(FooEnum::ONE), $foo->getEmbedded()->getEmbedded()->getEnum());
		Assert::assertNull($foo->getNullableEnum());
		Assert::assertNull($foo->getNotLoadedEmbedded());
	}

	public function testLoadEnumMissingEnumClass(): void
	{
		$this->expectException(\Doctrine\Common\Annotations\AnnotationException::class);
		$this->expectExceptionMessage('missingEnumClass');

		$this->callPostLoadEventOnEntity(new BarEntity());
	}

	public function testLoadEnumNonExistingEnumClass(): void
	{
		try {
			$this->callPostLoadEventOnEntity(new BazEntity());
			Assert::fail('Exception expected');
		} catch (\Consistence\Doctrine\Enum\NotEnumException $e) {
			Assert::assertSame('Consistence\Doctrine\Enum\NonExistingClass', $e->getEnumClass());
		}
	}

	public function testLoadEnumNotEnumClass(): void
	{
		try {
			$this->callPostLoadEventOnEntity(new BaxEntity());
			Assert::fail('Exception expected');
		} catch (\Consistence\Doctrine\Enum\NotEnumException $e) {
			Assert::assertSame(FooEntity::class, $e->getEnumClass());
		}
	}

	public function testLoadMultipleInstancesOfOneEntity(): void
	{
		$foo = new FooEntity();
		$iAmFooToo = new FooEntity();

		[$postLoadListener, $entityManager] = $this->getPostLoadListener();

		$postLoadListener->postLoad(new LifecycleEventArgs($foo, $entityManager));
		$postLoadListener->postLoad(new LifecycleEventArgs($iAmFooToo, $entityManager));

		Assert::assertSame(FooEnum::get(FooEnum::ONE), $foo->getEnum());
		Assert::assertSame(FooEnum::get(FooEnum::ONE), $iAmFooToo->getEnum());
	}

	private function callPostLoadEventOnEntity(object $entity): void
	{
		[$postLoadListener, $entityManager] = $this->getPostLoadListener();

		$loadEvent = new LifecycleEventArgs($entity, $entityManager);
		$postLoadListener->postLoad($loadEvent);
	}

	/**
	 * @return mixed[]
	 */
	private function getPostLoadListener(): array
	{
		$connectionParameters = [
			'driver' => 'pdo_mysql',
		];
		$config = Setup::createAnnotationMetadataConfiguration([__DIR__ . '/data'], true, null, null, false);
		$entityManager = EntityManager::create($connectionParameters, $config);
		/** @var \Doctrine\ORM\Mapping\Driver\AnnotationDriver $annotationDriver */
		$annotationDriver = $entityManager->getConfiguration()->getMetadataDriverImpl();

		return [
			new EnumPostLoadEntityListener($annotationDriver->getReader()),
			$entityManager,
		];
	}

}
