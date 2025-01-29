<?php

declare(strict_types = 1);

namespace Consistence\Doctrine\Enum\Type;

use Consistence\Enum\Enum;
use DateTimeImmutable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type as DoctrineType;
use Generator;
use PHPUnit\Framework\Assert;

class EnumTypeIntegrationTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @return mixed[][]|\Generator
	 */
	public function doctrineTypeRegistrationDataProvider(): Generator
	{
		yield 'boolean enum' => [
			'doctrineTypeClass' => BooleanEnumType::class,
			'enumClass' => FooBooleanEnum::class,
		];
		yield 'float enum' => [
			'doctrineTypeClass' => FloatEnumType::class,
			'enumClass' => FooFloatEnum::class,
		];
		yield 'integer enum' => [
			'doctrineTypeClass' => IntegerEnumType::class,
			'enumClass' => FooIntegerEnum::class,
		];
		yield 'string enum' => [
			'doctrineTypeClass' => StringEnumType::class,
			'enumClass' => FooStringEnum::class,
		];
	}

	/**
	 * @return mixed[][]|\Generator
	 */
	public function enumDataProvider(): Generator
	{
		foreach ($this->doctrineTypeRegistrationDataProvider() as $caseData) {
			$doctrineType = $caseData['doctrineTypeClass']::create($caseData['enumClass']);

			if (DoctrineType::getTypeRegistry()->has($doctrineType->getName())) {
				DoctrineType::getTypeRegistry()->override($doctrineType->getName(), $doctrineType);
			} else {
				DoctrineType::getTypeRegistry()->register($doctrineType->getName(), $doctrineType);
			}
		}

		yield 'float enum' => [
			'doctrineTypeName' => 'enum<Consistence\Doctrine\Enum\Type\FooFloatEnum>',
			'enum' => FooFloatEnum::get(FooFloatEnum::ONE),
			'scalarValue' => FooFloatEnum::ONE,
		];
		yield 'integer enum' => [
			'doctrineTypeName' => 'enum<Consistence\Doctrine\Enum\Type\FooIntegerEnum>',
			'enum' => FooIntegerEnum::get(FooIntegerEnum::ONE),
			'scalarValue' => FooIntegerEnum::ONE,
		];
		yield 'string enum' => [
			'doctrineTypeName' => 'enum<Consistence\Doctrine\Enum\Type\FooStringEnum>',
			'enum' => FooStringEnum::get(FooStringEnum::ONE),
			'scalarValue' => FooStringEnum::ONE,
		];
		yield 'boolean enum' => [
			'doctrineTypeName' => 'enum<Consistence\Doctrine\Enum\Type\FooBooleanEnum>',
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
	 * @return mixed[][]|\Generator
	 */
	public function convertDatabaseValueToEnumDataProvider(): Generator
	{
		foreach ($this->enumDataProvider() as $caseName => $caseData) {
			yield $caseName => [
				'doctrineType' => DoctrineType::getType($caseData['doctrineTypeName']),
				'value' => $caseData['scalarValue'],
				'expectedEnum' => $caseData['enum'],
			];
		}
	}

	/**
	 * @dataProvider convertDatabaseValueToEnumDataProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $doctrineType
	 * @param mixed $value
	 * @param \Consistence\Enum\Enum $expectedEnum
	 */
	public function testConvertDatabaseValueToEnum(DoctrineType $doctrineType, $value, Enum $expectedEnum): void
	{
		$platform = $this->createMock(AbstractPlatform::class);
		Assert::assertSame($expectedEnum, $doctrineType->convertToPHPValue($value, $platform));
	}

	/**
	 * @dataProvider enumTypeDataProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $doctrineType
	 */
	public function testConvertNullToPhp(DoctrineType $doctrineType): void
	{
		$platform = $this->createMock(AbstractPlatform::class);
		Assert::assertNull($doctrineType->convertToPHPValue(null, $platform));
	}

	/**
	 * @return \Doctrine\DBAL\Types\Type[][]|\Generator
	 */
	public function getNameDataProvider(): Generator
	{
		foreach ($this->enumDataProvider() as $caseName => $caseData) {
			yield $caseName => [
				'doctrineType' => DoctrineType::getType($caseData['doctrineTypeName']),
				'expectedName' => $caseData['doctrineTypeName'],
			];
		}
	}

	/**
	 * @dataProvider getNameDataProvider
	 *
	 * @param \Doctrine\DBAL\Types\Type $doctrineType
	 * @param string $expectedName
	 */
	public function testGetName(DoctrineType $doctrineType, string $expectedName): void
	{
		Assert::assertSame($expectedName, $doctrineType->getName());
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

	/**
	 * @return mixed[][]|\Generator
	 */
	public function doctrineTypeClassDataProvider(): Generator
	{
		foreach ($this->doctrineTypeRegistrationDataProvider() as $caseName => $caseData) {
			yield $caseName => [
				'doctrineTypeClass' => $caseData['doctrineTypeClass'],
			];
		}
	}

	/**
	 * @dataProvider doctrineTypeClassDataProvider
	 *
	 * @param string $doctrineTypeClass
	 */
	public function testCreateNotFromEnum(
		string $doctrineTypeClass
	): void
	{
		$notEnumClass = DateTimeImmutable::class;

		try {
			$doctrineTypeClass::create($notEnumClass);
			Assert::fail('Exception expected');
		} catch (\Consistence\Doctrine\Enum\Type\CannotCreateEnumTypeWithClassWhichIsNotEnumException $e) {
			Assert::assertSame($notEnumClass, $e->getEnumClass());
		}
	}

	/**
	 * @dataProvider doctrineTypeClassDataProvider
	 *
	 * @param string $doctrineTypeClass
	 */
	public function testConvertToDatabaseWithoutEnumClass(
		string $doctrineTypeClass
	): void
	{
		if (DoctrineType::hasType(__METHOD__)) {
			DoctrineType::overrideType(__METHOD__, $doctrineTypeClass);
		} else {
			DoctrineType::addType(__METHOD__, $doctrineTypeClass);
		}

		$platform = $this->createMock(AbstractPlatform::class);

		try {
			DoctrineType::getType(__METHOD__)->convertToDatabaseValue(null, $platform);
			Assert::fail('Exception expected');
		} catch (\Consistence\Doctrine\Enum\Type\CannotUseEnumTypeWithoutEnumClassException $e) {
			Assert::assertSame($doctrineTypeClass, $e->getDbalTypeClass());
		}
	}

	/**
	 * @dataProvider doctrineTypeClassDataProvider
	 *
	 * @param string $doctrineTypeClass
	 */
	public function testConvertToEnumWithoutEnumClass(
		string $doctrineTypeClass
	): void
	{
		if (DoctrineType::hasType(__METHOD__)) {
			DoctrineType::overrideType(__METHOD__, $doctrineTypeClass);
		} else {
			DoctrineType::addType(__METHOD__, $doctrineTypeClass);
		}

		$platform = $this->createMock(AbstractPlatform::class);

		try {
			DoctrineType::getType(__METHOD__)->convertToPHPValue(null, $platform);
			Assert::fail('Exception expected');
		} catch (\Consistence\Doctrine\Enum\Type\CannotUseEnumTypeWithoutEnumClassException $e) {
			Assert::assertSame($doctrineTypeClass, $e->getDbalTypeClass());
		}
	}

	/**
	 * @dataProvider doctrineTypeRegistrationDataProvider
	 *
	 * @param string $doctrineTypeClass
	 * @param string $enumClass
	 */
	public function testNormalizeNameToFormWithoutLeadingBackslash(
		string $doctrineTypeClass,
		string $enumClass
	): void
	{
		$doctrineTypeCreatedWithoutLeadingBackslash = $doctrineTypeClass::create($enumClass);
		$doctrineTypeCreatedWithLeadingBackslash = $doctrineTypeClass::create('\\' . $enumClass);

		Assert::assertSame(
			$doctrineTypeCreatedWithLeadingBackslash->getName(),
			$doctrineTypeCreatedWithoutLeadingBackslash->getName()
		);
	}

}
