<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum\Type;

use Consistence\Enum\Enum;
use DateTimeImmutable;
use Generator;
use PHPUnit\Framework\Assert;

class EnumTypeTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @return mixed[][]|\Generator
	 */
	public function enumDataProvider(): Generator
	{
		yield 'float enum' => [
			'name' => 'enum<Consistence\Doctrine\Enum\Type\FooFloatEnum>',
			'enum' => FooFloatEnum::get(FooFloatEnum::ONE),
			'scalarValue' => FooFloatEnum::ONE,
		];
		yield 'integer enum' => [
			'name' => 'enum<Consistence\Doctrine\Enum\Type\FooIntegerEnum>',
			'enum' => FooIntegerEnum::get(FooIntegerEnum::ONE),
			'scalarValue' => FooIntegerEnum::ONE,
		];
		yield 'string enum' => [
			'name' => 'enum<Consistence\Doctrine\Enum\Type\FooStringEnum>',
			'enum' => FooStringEnum::get(FooStringEnum::ONE),
			'scalarValue' => FooStringEnum::ONE,
		];
		yield 'boolean enum' => [
			'name' => 'enum<Consistence\Doctrine\Enum\Type\FooBooleanEnum>',
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
				'enum' => $caseData['enum'],
				'expectedValue' => $caseData['scalarValue'],
			];
		}
	}

	/**
	 * @dataProvider convertEnumToDatabaseDataProvider
	 *
	 * @param \Consistence\Enum\Enum $enum
	 * @param mixed $expectedValue
	 */
	public function testConvertEnumToDatabase(Enum $enum, $expectedValue): void
	{
		$enumType = new EnumType(get_class($enum));
		Assert::assertSame($expectedValue, $enumType->convertToDatabaseValue($enum));
	}

	/**
	 * @return \Doctrine\DBAL\Types\Type[][]|\Generator
	 */
	public function enumClassDataProvider(): Generator
	{
		foreach ($this->enumDataProvider() as $caseName => $caseData) {
			yield $caseName => [
				'enumClass' => get_class($caseData['enum']),
			];
		}
	}

	/**
	 * @dataProvider enumClassDataProvider
	 *
	 * @param string $enumClass
	 */
	public function testConvertNullToDatabase(string $enumClass): void
	{
		$enumType = new EnumType($enumClass);
		Assert::assertNull($enumType->convertToDatabaseValue(null));
	}

	/**
	 * @return mixed[][]|\Generator
	 */
	public function convertDatabaseValueToEnumDataProvider(): Generator
	{
		foreach ($this->enumDataProvider() as $caseName => $caseData) {
			yield $caseName => [
				'value' => $caseData['scalarValue'],
				'expectedEnum' => $caseData['enum'],
			];
		}
	}

	/**
	 * @dataProvider convertDatabaseValueToEnumDataProvider
	 *
	 * @param mixed $value
	 * @param \Consistence\Enum\Enum $expectedEnum
	 */
	public function testConvertDatabaseValueToEnum($value, Enum $expectedEnum): void
	{
		$enumType = new EnumType(get_class($expectedEnum));
		Assert::assertSame($expectedEnum, $enumType->convertToPHPValue($value));
	}

	/**
	 * @dataProvider enumClassDataProvider
	 *
	 * @param string $enumClass
	 */
	public function testConvertNullToPhp(string $enumClass): void
	{
		$enumType = new EnumType($enumClass);
		Assert::assertNull($enumType->convertToPHPValue(null));
	}

	/**
	 * @return \Doctrine\DBAL\Types\Type[][]|\Generator
	 */
	public function nameDataProvider(): Generator
	{
		foreach ($this->enumDataProvider() as $caseName => $caseData) {
			yield $caseName => [
				'enumClass' => get_class($caseData['enum']),
				'expectedName' => $caseData['name'],
			];
		}
	}

	/**
	 * @dataProvider nameDataProvider
	 *
	 * @param string $enumClass
	 * @param string $expectedName
	 */
	public function testFormatName(string $enumClass, string $expectedName): void
	{
		Assert::assertSame($expectedName, EnumType::formatName($enumClass));
	}

	/**
	 * @dataProvider nameDataProvider
	 *
	 * @param string $enumClass
	 * @param string $expectedName
	 */
	public function testGetName(string $enumClass, string $expectedName): void
	{
		$enumType = new EnumType($enumClass);
		Assert::assertSame($expectedName, $enumType->getName());
	}

	public function testCreateNotFromEnum(): void
	{
		$notEnumClass = DateTimeImmutable::class;

		try {
			new EnumType($notEnumClass);
			Assert::fail('Exception expected');
		} catch (\Consistence\Doctrine\Enum\Type\CannotCreateEnumTypeWithClassWhichIsNotEnumException $e) {
			Assert::assertSame($notEnumClass, $e->getEnumClass());
		}
	}

	public function testFormatEnumTypeNameWithNotEnum(): void
	{
		$notEnumClass = DateTimeImmutable::class;

		try {
			EnumType::formatName($notEnumClass);
			Assert::fail('Exception expected');
		} catch (\Consistence\Doctrine\Enum\Type\CannotFormatEnumTypeNameWithClassWhichIsNotEnumException $e) {
			Assert::assertSame($notEnumClass, $e->getEnumClass());
		}
	}

	/**
	 * @dataProvider enumClassDataProvider
	 *
	 * @param string $enumClass
	 */
	public function testNormalizeNameToFormWithoutLeadingBackslash(
		string $enumClass
	): void
	{
		$enumTypeCreatedWithoutLeadingBackslash = new EnumType($enumClass);
		$enumTypeCreatedWithLeadingBackslash = new EnumType('\\' . $enumClass);

		Assert::assertSame(
			$enumTypeCreatedWithLeadingBackslash->getName(),
			$enumTypeCreatedWithoutLeadingBackslash->getName()
		);
	}

}
