<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Tools\Setup;

class LoadEnumToEntityIntegrationTest extends \PHPUnit\Framework\TestCase
{

	public function testLoadEnumToEntity()
	{
		$foo = new FooEntity();
		$this->callPostLoadEventOnEntity($foo);

		$this->assertSame(FooEnum::get(FooEnum::ONE), $foo->getEnum());
	}

	public function testLoadNullEnumToEntity()
	{
		$foo = new FooEntity();
		$this->callPostLoadEventOnEntity($foo);

		$this->assertNull($foo->getNullableEnum());
	}

	public function testMultipleLoadEvents()
	{
		$foo = new FooEntity();
		$this->callPostLoadEventOnEntity($foo);
		$this->callPostLoadEventOnEntity($foo);

		$this->assertSame(FooEnum::get(FooEnum::ONE), $foo->getEnum());
	}

	public function testLoadEnumClassWithoutNamespace()
	{
		$foo = new FooEntity();
		$this->callPostLoadEventOnEntity($foo);

		$this->assertSame(FooEnum::get(FooEnum::ONE), $foo->getWithoutNamespace());
	}

	public function testLoadEnumInEmbeddable()
	{
		$foo = new FooEntity();
		$this->callPostLoadEventOnEntity($foo);

		$this->assertSame(FooEnum::get(FooEnum::ONE), $foo->getEmbedded()->getEnum());
	}

	public function testLoadEnumInEmbeddableWeNeedToGoDeeper()
	{
		$foo = new FooEntity();
		$this->callPostLoadEventOnEntity($foo);

		$this->assertSame(FooEnum::get(FooEnum::ONE), $foo->getEmbedded()->getEmbedded()->getEnum());
	}

	public function testLoadEnumMissingEnumClass()
	{
		$this->expectException(\Doctrine\Common\Annotations\AnnotationException::class);
		$this->expectExceptionMessage('missingEnumClass');

		$this->callPostLoadEventOnEntity(new BarEntity());
	}

	public function testLoadEnumNonExistingEnumClass()
	{
		try {
			$this->callPostLoadEventOnEntity(new BazEntity());
		} catch (\Consistence\Doctrine\Enum\NotEnumException $e) {
			$this->assertSame('Consistence\Doctrine\Enum\NonExistingClass', $e->getEnumClass());
		}
	}

	public function testLoadEnumNotEnumClass()
	{
		try {
			$this->callPostLoadEventOnEntity(new BaxEntity());
		} catch (\Consistence\Doctrine\Enum\NotEnumException $e) {
			$this->assertSame(FooEntity::class, $e->getEnumClass());
		}
	}

	/**
	 * @param object $entity
	 */
	private function callPostLoadEventOnEntity($entity)
	{
		$connectionParameters = [
			'driver' => 'pdo_mysql',
		];
		$config = Setup::createAnnotationMetadataConfiguration([__DIR__ . '/data'], true, null, null, false);
		$entityManager = EntityManager::create($connectionParameters, $config);
		/** @var \Doctrine\ORM\Mapping\Driver\AnnotationDriver $annotationDriver */
		$annotationDriver = $entityManager->getConfiguration()->getMetadataDriverImpl();

		$postLoadListener = new EnumPostLoadEntityListener($annotationDriver->getReader());

		$loadEvent = new LifecycleEventArgs($entity, $entityManager);
		$postLoadListener->postLoad($loadEvent);
	}

}
