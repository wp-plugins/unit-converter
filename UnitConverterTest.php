<?php

// Stub the WordPress functions called.
function add_action() {}

require_once dirname(__FILE__) . '/unit-converter.php';

class UnitConverterTest extends PHPUnit_Framework_TestCase
{
	public function setup()
	{
		UnitConverter::setOpts(array('display_mode' => 'parentheses'));
	}

	public function testBasicConversion()
	{
		$data = '10kg';
		$actual = UnitConverter::filter($data);
		$expected = '10kg (22.05 pounds)';
		$this->assertEquals($expected, $actual);
	}

	public function testLongConversion()
	{
		$data = '10 kilograms';
		$actual = UnitConverter::filter($data);
		$expected = '10 kilograms (22.05 pounds)';
		$this->assertEquals($expected, $actual);
	}

	public function testSingularConversion()
	{
		$data = '2.205lbs';
		$actual = UnitConverter::filter($data);
		$expected = '2.205lbs (1 kilogram)';
		$this->assertEquals($expected, $actual);
	}

	public function testMultipleConversion()
	{
		$data = '10kg 10ft';
		$actual = UnitConverter::filter($data);
		$expected = '10kg (22.05 pounds) 10ft (3.05 metres)';
		$this->assertEquals($expected, $actual);
	}

	public function testMultipleIdenticalReplacements()
	{
		$data = '10kg 10ft 10kg';
		$actual = UnitConverter::filter($data);
		$expected = '10kg (22.05 pounds) 10ft (3.05 metres) 10kg (22.05 pounds)';
		$this->assertEquals($expected, $actual);
	}

	public function testIdenticalUnitReplacementForDifferentAmounts()
	{
		$data = '1kg 2kg';
		$actual = UnitConverter::filter($data);
		$expected = '1kg (2.2 pounds) 2kg (4.41 pounds)';
		$this->assertEquals($expected, $actual);
	}

	public function testUnitDetectionBoundary()
	{
		$data = '1kg (2kg) 3kg.';
		$actual = UnitConverter::filter($data);
		$expected = '1kg (2.2 pounds) (2kg (4.41 pounds)) 3kg (6.61 pounds).';
		$this->assertEquals($expected, $actual);
	}

	public function testDecimalPoint()
	{
		$data = '5kg 1.5kg';
		$actual = UnitConverter::filter($data);
		$expected = '5kg (11.02 pounds) 1.5kg (3.31 pounds)';
		$this->assertEquals($expected, $actual);
	}

	public function testCaseSensitivity()
	{
		$data = '5KG 5L';
		$actual = UnitConverter::filter($data);
		$expected = '5KG 5L (1.32 gallons)';
		$this->assertEquals($expected, $actual);
		$data = '5kg 5l';
		$actual = UnitConverter::filter($data);
		$expected = '5kg (11.02 pounds) 5l';
		$this->assertEquals($expected, $actual);
	}

	public function testKilojoulesToCalories()
	{
		$data = '5 cals 10 kj';
		$actual = UnitConverter::filter($data);
		$expected = '5 cals (20.92 kilojoules) 10 kj (2.39 calories)';
		$this->assertEquals($expected, $actual);
	}

	public function testGramsToOunces()
	{
		$data = '5 g 10 oz';
		$actual = UnitConverter::filter($data);
		$expected = '5 g (0.18 ounces) 10 oz (283.5 grams)';
		$this->assertEquals($expected, $actual);
	}

	public function testMillilitresToFluidOunces()
	{
		$data = '5 mL 10 fl oz';
		$actual = UnitConverter::filter($data);
		$expected = '5 mL (0.17 fluid ounces) 10 fl oz (295.74 millilitres)';
		$this->assertEquals($expected, $actual);
	}

	public function testCelsiusToFahrenheit()
	{
		$data = '5 C 10 F';
		$actual = UnitConverter::filter($data);
		$expected = '5 C (41 degrees Fahrenheit) 10 F (-12.22 degrees Celsius)';
		$this->assertEquals($expected, $actual);
	}

	public function testDegreeSymbols()
	{
		// Test regular degrees symbol.
		$data = '5°C';
		$actual = UnitConverter::filter($data);
		$expected = '5°C (41 degrees Fahrenheit)';
		$this->assertEquals($expected, $actual);
		// Test HTML entity.
		$data = '5&deg;C';
		$actual = UnitConverter::filter($data);
		$expected = '5&deg;C (41 degrees Fahrenheit)';
		$this->assertEquals($expected, $actual);
		// Test Unicode symbol of each.
		$data = '5℃ 10℉';
		$actual = UnitConverter::filter($data);
		$expected = '5℃ (41 degrees Fahrenheit) 10℉ (-12.22 degrees Celsius)';
		$this->assertEquals($expected, $actual);

	}
}
