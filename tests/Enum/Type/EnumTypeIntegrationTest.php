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
		yield 'float enum' => [
			'type' => DoctrineType::getType(FloatEnumType::NAME),
			'enum' => FooFloatEnum::get(FooFloatEnum::ONE),
			'expectedValue' => FooFloatEnum::ONE,
		];
		yield 'integer enum' => [
			'type' => DoctrineType::getType(IntegerEnumType::NAME),
			'enum' => FooIntegerEnum::get(FooIntegerEnum::ONE),
			'expectedValue' => FooIntegerEnum::ONE,
		];
		yield 'string enum' => [
			'type' => DoctrineType::getType(StringEnumType::NAME),
			'enum' => FooStringEnum::get(FooStringEnum::ONE),
			'expectedValue' => FooStringEnum::ONE,
		];
		yield 'boolean enum' => [
			'type' => DoctrineType::getType(BooleanEnumType::NAME),
			'enum' => FooBooleanEnum::get(FooBooleanEnum::ENABLED),
			'expectedValue' => FooBooleanEnum::ENABLED,
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
		yield 'float enum' => [
			'type' => DoctrineType::getType(FloatEnumType::NAME),
		];
		yield 'integer enum' => [
			'type' => DoctrineType::getType(IntegerEnumType::NAME),
		];
		yield 'string enum' => [
			'type' => DoctrineType::getType(StringEnumType::NAME),
		];
		yield 'boolean enum' => [
			'type' => DoctrineType::getType(BooleanEnumType::NAME),
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
		yield 'float enum' => [
			'type' => DoctrineType::getType(FloatEnumType::NAME),
			'scalarValue' => FooFloatEnum::ONE,
		];
		yield 'integer enum' => [
			'type' => DoctrineType::getType(IntegerEnumType::NAME),
			'scalarValue' => FooIntegerEnum::ONE,
		];
		yield 'string enum' => [
			'type' => DoctrineType::getType(StringEnumType::NAME),
			'scalarValue' => FooStringEnum::ONE,
		];
		yield 'boolean enum' => [
			'type' => DoctrineType::getType(BooleanEnumType::NAME),
			'scalarValue' => FooBooleanEnum::ENABLED,
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
