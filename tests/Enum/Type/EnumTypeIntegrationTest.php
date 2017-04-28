<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum\Type;

use Consistence\Enum\Enum;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type as DoctrineType;

class EnumTypeIntegrationTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @return mixed[][]
	 */
	public function convertEnumToDatabaseProvider(): array
	{
		return [
			[DoctrineType::getType(IntegerEnumType::NAME), FooFloatEnum::get(FooFloatEnum::ONE), FooFloatEnum::ONE],
			[DoctrineType::getType(IntegerEnumType::NAME), FooIntegerEnum::get(FooIntegerEnum::ONE), FooIntegerEnum::ONE],
			[DoctrineType::getType(StringEnumType::NAME), FooStringEnum::get(FooStringEnum::ONE), FooStringEnum::ONE],
		];
	}

	/**
	 * @dataProvider convertEnumToDatabaseProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $type
	 * @param \Consistence\Enum\Enum $enum
	 * @param mixed $expectedValue
	 */
	public function testConvertEnumToDatabase(DoctrineType $type, Enum $enum, $expectedValue)
	{
		$platform = $this->createMock(AbstractPlatform::class);
		$this->assertSame($expectedValue, $type->convertToDatabaseValue($enum, $platform));
	}

	/**
	 * @return \Doctrine\DBAL\Types\Type[][]
	 */
	public function enumTypesProvider(): array
	{
		return [
			[DoctrineType::getType(FloatEnumType::NAME)],
			[DoctrineType::getType(IntegerEnumType::NAME)],
			[DoctrineType::getType(StringEnumType::NAME)],
		];
	}

	/**
	 * @dataProvider enumTypesProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $type
	 */
	public function testConvertNullToDatabase(DoctrineType $type)
	{
		$platform = $this->createMock(AbstractPlatform::class);
		$this->assertNull($type->convertToDatabaseValue(null, $platform));
	}

	/**
	 * @return mixed[][]
	 */
	public function convertScalarValueToDatabaseProvider(): array
	{
		return [
			[DoctrineType::getType(FloatEnumType::NAME), FooFloatEnum::ONE],
			[DoctrineType::getType(IntegerEnumType::NAME), FooIntegerEnum::ONE],
			[DoctrineType::getType(StringEnumType::NAME), FooStringEnum::ONE],
		];
	}

	/**
	 * @dataProvider convertScalarValueToDatabaseProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $type
	 * @param mixed $scalarValue
	 */
	public function testConvertScalarValueToDatabase(DoctrineType $type, $scalarValue)
	{
		$platform = $this->createMock(AbstractPlatform::class);
		$this->assertSame($scalarValue, $type->convertToDatabaseValue($scalarValue, $platform));
	}

	/**
	 * @return string[][]
	 */
	public function enumTypeClassesProvider(): array
	{
		return [
			[FloatEnumType::class],
			[IntegerEnumType::class],
			[StringEnumType::class],
		];
	}

	/**
	 * @dataProvider enumTypeClassesProvider
	 *
	 * @param string $typeClass
	 */
	public function testGetName(string $typeClass)
	{
		$this->assertSame($typeClass::NAME, DoctrineType::getType($typeClass::NAME)->getName());
	}

	/**
	 * @dataProvider enumTypesProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $type
	 */
	public function testRequiresSqlCommentHint(DoctrineType $type)
	{
		$platform = $this->createMock(AbstractPlatform::class);
		$this->assertTrue($type->requiresSQLCommentHint($platform));
	}

}
