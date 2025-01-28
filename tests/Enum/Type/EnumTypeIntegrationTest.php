<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum\Type;

use Consistence\Enum\Enum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type as DbalType;
use Generator;
use PHPUnit\Framework\Assert;

class EnumTypeIntegrationTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @return mixed[][]|\Generator
	 */
	public function enumDataProvider(): Generator
	{
		yield 'float enum' => [
			'dbalTypeName' => FloatEnumType::NAME,
			'enum' => FooFloatEnum::get(FooFloatEnum::ONE),
			'scalarValue' => FooFloatEnum::ONE,
		];
		yield 'integer enum' => [
			'dbalTypeName' => IntegerEnumType::NAME,
			'enum' => FooIntegerEnum::get(FooIntegerEnum::ONE),
			'scalarValue' => FooIntegerEnum::ONE,
		];
		yield 'string enum' => [
			'dbalTypeName' => StringEnumType::NAME,
			'enum' => FooStringEnum::get(FooStringEnum::ONE),
			'scalarValue' => FooStringEnum::ONE,
		];
		yield 'boolean enum' => [
			'dbalTypeName' => BooleanEnumType::NAME,
			'enum' => FooBooleanEnum::get(FooBooleanEnum::ENABLED),
			'scalarValue' => FooBooleanEnum::ENABLED,
		];
	}

	/**
	 * @return mixed[][]|\Generator
	 */
	public function convertEnumToDatabaseDataProvider(): Generator
	{
		foreach ($this->enumDataProvider() as $caseName => $caseData) {
			yield $caseName => [
				'dbalType' => DbalType::getType($caseData['dbalTypeName']),
				'enum' => $caseData['enum'],
				'expectedValue' => $caseData['scalarValue'],
			];
		}
	}

	/**
	 * @dataProvider convertEnumToDatabaseDataProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $dbalType
	 * @param \Consistence\Enum\Enum $enum
	 * @param mixed $expectedValue
	 */
	public function testConvertEnumToDatabase(DbalType $dbalType, Enum $enum, $expectedValue): void
	{
		$platform = $this->createMock(AbstractPlatform::class);
		Assert::assertSame($expectedValue, $dbalType->convertToDatabaseValue($enum, $platform));
	}

	/**
	 * @return \Doctrine\DBAL\Types\Type[][]|\Generator
	 */
	public function enumTypeDataProvider(): Generator
	{
		foreach ($this->enumDataProvider() as $caseName => $caseData) {
			yield $caseName => [
				'dbalType' => DbalType::getType($caseData['dbalTypeName']),
			];
		}
	}

	/**
	 * @dataProvider enumTypeDataProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $dbalType
	 */
	public function testConvertNullToDatabase(DbalType $dbalType): void
	{
		$platform = $this->createMock(AbstractPlatform::class);
		Assert::assertNull($dbalType->convertToDatabaseValue(null, $platform));
	}

	/**
	 * @dataProvider enumTypeDataProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $dbalType
	 */
	public function testGetName(DbalType $dbalType): void
	{
		Assert::assertSame($dbalType::NAME, $dbalType->getName());
	}

	/**
	 * @dataProvider enumTypeDataProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $dbalType
	 */
	public function testRequiresSqlCommentHint(DbalType $dbalType): void
	{
		$platform = $this->createMock(AbstractPlatform::class);
		Assert::assertTrue($dbalType->requiresSQLCommentHint($platform));
	}

}
