<?php

use urmaul\formatter\ValueFormatter;

class ValueFormatterTest extends PHPUnit_Framework_TestCase
{
	public function testFormatValueProvider()
	{
		return array(
			array("123\n456", array(array('cut', "\n")), '123'),
			array("123\n456", array(array('cut', "\n"), array('replace', '23', '45')), '145'),
		);
	}
	
	/**
	 * @dataProvider testFormatValueProvider
	 * @param mixed $value
	 * @param array $config
	 * @param mixed $expected
	 */
	public function testFormatValue($value, $config, $expected)
	{
		$formatter = new ValueFormatter();
		$actual = $formatter->format($value, $config);
		$this->assertSame($expected, $actual);
	}
}
