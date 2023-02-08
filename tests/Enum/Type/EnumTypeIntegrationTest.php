<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum\Type;

use Consistence\Enum\Enum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type as DoctrineType;
use Generator;
use PHPUnit\Framework\Assert;

class EnumTypeIntegrationTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @return mixed[][]|\Generator
	 */
	public function convertEnumToDatabaseDataProvider(): Generator
	{
		yield [
			DoctrineType::getType(FloatEnumType::NAME),
			FooFloatEnum::get(FooFloatEnum::ONE),
			FooFloatEnum::ONE,
		];
		yield [
			DoctrineType::getType(IntegerEnumType::NAME),
			FooIntegerEnum::get(FooIntegerEnum::ONE),
			FooIntegerEnum::ONE,
		];
		yield [
			DoctrineType::getType(StringEnumType::NAME),
			FooStringEnum::get(FooStringEnum::ONE),
			FooStringEnum::ONE,
		];
		yield [
			DoctrineType::getType(BooleanEnumType::NAME),
			FooBooleanEnum::get(FooBooleanEnum::ENABLED),
			FooBooleanEnum::ENABLED,
		];
	}

	/**
	 * @dataProvider convertEnumToDatabaseDataProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $type
	 * @param \Consistence\Enum\Enum $enum
	 * @param mixed $expectedValue
	 */
	public function testConvertEnumToDatabase(DoctrineType $type, Enum $enum, $expectedValue): void
	{
		$platform = $this->createMock(AbstractPlatform::class);
		Assert::assertSame($expectedValue, $type->convertToDatabaseValue($enum, $platform));
	}

	/**
	 * @return \Doctrine\DBAL\Types\Type[][]|\Generator
	 */
	public function enumTypeDataProvider(): Generator
	{
		yield [
			DoctrineType::getType(FloatEnumType::NAME),
		];
		yield [
			DoctrineType::getType(IntegerEnumType::NAME),
		];
		yield [
			DoctrineType::getType(StringEnumType::NAME),
		];
		yield [
			DoctrineType::getType(BooleanEnumType::NAME),
		];
	}

	/**
	 * @dataProvider enumTypeDataProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $type
	 */
	public function testConvertNullToDatabase(DoctrineType $type): void
	{
		$platform = $this->createMock(AbstractPlatform::class);
		Assert::assertNull($type->convertToDatabaseValue(null, $platform));
	}

	/**
	 * @return mixed[][]|\Generator
	 */
	public function convertScalarValueToDatabaseDataProvider(): Generator
	{
		yield [
			DoctrineType::getType(FloatEnumType::NAME),
			FooFloatEnum::ONE,
		];
		yield [
			DoctrineType::getType(IntegerEnumType::NAME),
			FooIntegerEnum::ONE,
		];
		yield [
			DoctrineType::getType(StringEnumType::NAME),
			FooStringEnum::ONE,
		];
		yield [
			DoctrineType::getType(BooleanEnumType::NAME),
			FooBooleanEnum::ENABLED,
		];
	}

	/**
	 * @dataProvider convertScalarValueToDatabaseDataProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $type
	 * @param mixed $scalarValue
	 */
	public function testConvertScalarValueToDatabase(DoctrineType $type, $scalarValue): void
	{
		$platform = $this->createMock(AbstractPlatform::class);
		Assert::assertSame($scalarValue, $type->convertToDatabaseValue($scalarValue, $platform));
	}

	/**
	 * @dataProvider enumTypeDataProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $type
	 */
	public function testGetName(DoctrineType $type): void
	{
		Assert::assertSame($type::NAME, $type->getName());
	}

	/**
	 * @dataProvider enumTypeDataProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $type
	 */
	public function testRequiresSqlCommentHint(DoctrineType $type): void
	{
		$platform = $this->createMock(AbstractPlatform::class);
		Assert::assertTrue($type->requiresSQLCommentHint($platform));
	}

}
