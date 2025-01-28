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
	public function enumDataProvider(): Generator
	{
		yield 'float enum' => [
			'doctrineTypeName' => FloatEnumType::NAME,
			'enum' => FooFloatEnum::get(FooFloatEnum::ONE),
			'scalarValue' => FooFloatEnum::ONE,
		];
		yield 'integer enum' => [
			'doctrineTypeName' => IntegerEnumType::NAME,
			'enum' => FooIntegerEnum::get(FooIntegerEnum::ONE),
			'scalarValue' => FooIntegerEnum::ONE,
		];
		yield 'string enum' => [
			'doctrineTypeName' => StringEnumType::NAME,
			'enum' => FooStringEnum::get(FooStringEnum::ONE),
			'scalarValue' => FooStringEnum::ONE,
		];
		yield 'boolean enum' => [
			'doctrineTypeName' => BooleanEnumType::NAME,
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
				'doctrineType' => DoctrineType::getType($caseData['doctrineTypeName']),
				'enum' => $caseData['enum'],
				'expectedValue' => $caseData['scalarValue'],
			];
		}
	}

	/**
	 * @dataProvider convertEnumToDatabaseDataProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $doctrineType
	 * @param \Consistence\Enum\Enum $enum
	 * @param mixed $expectedValue
	 */
	public function testConvertEnumToDatabase(DoctrineType $doctrineType, Enum $enum, $expectedValue): void
	{
		$platform = $this->createMock(AbstractPlatform::class);
		Assert::assertSame($expectedValue, $doctrineType->convertToDatabaseValue($enum, $platform));
	}

	/**
	 * @return \Doctrine\DBAL\Types\Type[][]|\Generator
	 */
	public function enumTypeDataProvider(): Generator
	{
		foreach ($this->enumDataProvider() as $caseName => $caseData) {
			yield $caseName => [
				'doctrineType' => DoctrineType::getType($caseData['doctrineTypeName']),
			];
		}
	}

	/**
	 * @dataProvider enumTypeDataProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $doctrineType
	 */
	public function testConvertNullToDatabase(DoctrineType $doctrineType): void
	{
		$platform = $this->createMock(AbstractPlatform::class);
		Assert::assertNull($doctrineType->convertToDatabaseValue(null, $platform));
	}

	/**
	 * @dataProvider enumTypeDataProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $doctrineType
	 */
	public function testGetName(DoctrineType $doctrineType): void
	{
		Assert::assertSame($doctrineType::NAME, $doctrineType->getName());
	}

	/**
	 * @dataProvider enumTypeDataProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $doctrineType
	 */
	public function testRequiresSqlCommentHint(DoctrineType $doctrineType): void
	{
		$platform = $this->createMock(AbstractPlatform::class);
		Assert::assertTrue($doctrineType->requiresSQLCommentHint($platform));
	}

}
