<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum\Type;

use Consistence\Enum\Enum;
use DateTimeImmutable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type as DbalType;
use Generator;
use PHPUnit\Framework\Assert;

class EnumTypeIntegrationTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @return mixed[][]|\Generator
	 */
	public function dbalTypeRegistrationDataProvider(): Generator
	{
		yield 'boolean enum' => [
			'dbalTypeClass' => BooleanEnumType::class,
			'enumClass' => FooBooleanEnum::class,
		];
		yield 'float enum' => [
			'dbalTypeClass' => FloatEnumType::class,
			'enumClass' => FooFloatEnum::class,
		];
		yield 'integer enum' => [
			'dbalTypeClass' => IntegerEnumType::class,
			'enumClass' => FooIntegerEnum::class,
		];
		yield 'string enum' => [
			'dbalTypeClass' => StringEnumType::class,
			'enumClass' => FooStringEnum::class,
		];
	}

	/**
	 * @return mixed[][]|\Generator
	 */
	public function enumDataProvider(): Generator
	{
		foreach ($this->dbalTypeRegistrationDataProvider() as $caseData) {
			$dbalType = $caseData['dbalTypeClass']::create($caseData['enumClass']);

			if (DbalType::getTypeRegistry()->has($dbalType->getName())) {
				DbalType::getTypeRegistry()->override($dbalType->getName(), $dbalType);
			} else {
				DbalType::getTypeRegistry()->register($dbalType->getName(), $dbalType);
			}
		}

		yield 'float enum' => [
			'dbalTypeName' => 'enum<Consistence\Doctrine\Enum\Type\FooFloatEnum>',
			'enum' => FooFloatEnum::get(FooFloatEnum::ONE),
			'scalarValue' => FooFloatEnum::ONE,
		];
		yield 'integer enum' => [
			'dbalTypeName' => 'enum<Consistence\Doctrine\Enum\Type\FooIntegerEnum>',
			'enum' => FooIntegerEnum::get(FooIntegerEnum::ONE),
			'scalarValue' => FooIntegerEnum::ONE,
		];
		yield 'string enum' => [
			'dbalTypeName' => 'enum<Consistence\Doctrine\Enum\Type\FooStringEnum>',
			'enum' => FooStringEnum::get(FooStringEnum::ONE),
			'scalarValue' => FooStringEnum::ONE,
		];
		yield 'boolean enum' => [
			'dbalTypeName' => 'enum<Consistence\Doctrine\Enum\Type\FooBooleanEnum>',
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
	 * @return mixed[][]|\Generator
	 */
	public function convertDatabaseValueToEnumDataProvider(): Generator
	{
		foreach ($this->enumDataProvider() as $caseName => $caseData) {
			yield $caseName => [
				'dbalType' => DbalType::getType($caseData['dbalTypeName']),
				'value' => $caseData['scalarValue'],
				'expectedEnum' => $caseData['enum'],
			];
		}
	}

	/**
	 * @dataProvider convertDatabaseValueToEnumDataProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $dbalType
	 * @param mixed $value
	 * @param \Consistence\Enum\Enum $expectedEnum
	 */
	public function testConvertDatabaseValueToEnum(DbalType $dbalType, $value, Enum $expectedEnum): void
	{
		$platform = $this->getMockForAbstractClass(AbstractPlatform::class);
		Assert::assertSame($expectedEnum, $dbalType->convertToPHPValue($value, $platform));
	}

	/**
	 * @dataProvider enumTypeDataProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $dbalType
	 */
	public function testConvertNullToPhp(DbalType $dbalType): void
	{
		$platform = $this->getMockForAbstractClass(AbstractPlatform::class);
		Assert::assertNull($dbalType->convertToPHPValue(null, $platform));
	}

	/**
	 * @return \Doctrine\DBAL\Types\Type[][]|\Generator
	 */
	public function getNameDataProvider(): Generator
	{
		foreach ($this->enumDataProvider() as $caseName => $caseData) {
			yield $caseName => [
				'dbalType' => DbalType::getType($caseData['dbalTypeName']),
				'expectedName' => $caseData['dbalTypeName'],
			];
		}
	}

	/**
	 * @dataProvider getNameDataProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $dbalType
	 * @param string $expectedName
	 */
	public function testGetName(DbalType $dbalType, string $expectedName): void
	{
		Assert::assertSame($expectedName, $dbalType->getName());
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

	/**
	 * @return mixed[][]|\Generator
	 */
	public function dbalTypeClassDataProvider(): Generator
	{
		foreach ($this->dbalTypeRegistrationDataProvider() as $caseName => $caseData) {
			yield $caseName => [
				'dbalTypeClass' => $caseData['dbalTypeClass'],
			];
		}
	}

	/**
	 * @dataProvider dbalTypeClassDataProvider
	 *
	 * @param string $dbalTypeClass
	 */
	public function testCreateNotFromEnum(
		string $dbalTypeClass
	): void
	{
		$notEnumClass = DateTimeImmutable::class;

		try {
			$dbalTypeClass::create($notEnumClass);
			Assert::fail('Exception expected');
		} catch (\Consistence\Doctrine\Enum\Type\CannotCreateEnumTypeWithClassWhichIsNotEnumException $e) {
			Assert::assertSame($notEnumClass, $e->getEnumClass());
		}
	}

	/**
	 * @dataProvider dbalTypeClassDataProvider
	 *
	 * @param string $dbalTypeClass
	 */
	public function testConvertToDatabaseWithoutEnumClass(
		string $dbalTypeClass
	): void
	{
		if (DbalType::hasType(__METHOD__)) {
			DbalType::overrideType(__METHOD__, $dbalTypeClass);
		} else {
			DbalType::addType(__METHOD__, $dbalTypeClass);
		}

		$platform = $this->createMock(AbstractPlatform::class);

		try {
			DbalType::getType(__METHOD__)->convertToDatabaseValue(null, $platform);
			Assert::fail('Exception expected');
		} catch (\Consistence\Doctrine\Enum\Type\CannotUseEnumTypeWithoutEnumClassException $e) {
			Assert::assertSame($dbalTypeClass, $e->getDbalTypeClass());
		}
	}

	/**
	 * @dataProvider dbalTypeClassDataProvider
	 *
	 * @param string $dbalTypeClass
	 */
	public function testConvertToEnumWithoutEnumClass(
		string $dbalTypeClass
	): void
	{
		if (DbalType::hasType(__METHOD__)) {
			DbalType::overrideType(__METHOD__, $dbalTypeClass);
		} else {
			DbalType::addType(__METHOD__, $dbalTypeClass);
		}

		$platform = $this->getMockForAbstractClass(AbstractPlatform::class);

		try {
			DbalType::getType(__METHOD__)->convertToPHPValue(null, $platform);
			Assert::fail('Exception expected');
		} catch (\Consistence\Doctrine\Enum\Type\CannotUseEnumTypeWithoutEnumClassException $e) {
			Assert::assertSame($dbalTypeClass, $e->getDbalTypeClass());
		}
	}

	/**
	 * @dataProvider dbalTypeRegistrationDataProvider
	 *
	 * @param string $dbalTypeClass
	 * @param string $enumClass
	 */
	public function testNormalizeNameToFormWithoutLeadingBackslash(
		string $dbalTypeClass,
		string $enumClass
	): void
	{
		$dbalTypeCreatedWithoutLeadingBackslash = $dbalTypeClass::create($enumClass);
		$dbalTypeCreatedWithLeadingBackslash = $dbalTypeClass::create('\\' . $enumClass);

		Assert::assertSame(
			$dbalTypeCreatedWithLeadingBackslash->getName(),
			$dbalTypeCreatedWithoutLeadingBackslash->getName()
		);
	}

}
