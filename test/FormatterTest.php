<?php

use urmaul\formatter\Formatter;

class FormatterTest extends PHPUnit_Framework_TestCase
{
	public function testFormatByConfigProvider()
	{
		return array(
			array(
				array('a' => '123 456', 'b' => '78 90'),
				array(),
				array('a' => '123 456', 'b' => '78 90'),
			),
			array(
				array('a' => '123 456', 'b' => '78 90'),
				array(
					array('a', 'cut', ' '),
				),
				array('a' => '123', 'b' => '78 90'),
			),
			array(
				array('a' => '123 456', 'b' => '78 90'),
				array(
					array('a,b', 'cut', ' '),
				),
				array('a' => '123', 'b' => '78'),
			),
			array(
				array('a' => '123 456', 'b' => '78 90'),
				array(
					array('a, b', 'cut', ' '),
				),
				array('a' => '123', 'b' => '78'),
			),
			array(
				array('a' => '123 456', 'b' => '78 90'),
				array(
					array('a', 'cut', ' '),
					array('b', 'cut', ' '),
				),
				array('a' => '123', 'b' => '78'),
			),
		);
	}
	
	/**
	 * @dataProvider testFormatByConfigProvider
	 * @param array $values
	 * @param array $config
	 * @param mixed $expected
	 */
	public function testFormatByConfig($values, $config, $expected)
	{
		$formatter = new Formatter($config);
		$actual = $formatter->format($values);
		$this->assertEquals($expected, $actual);
	}
	
	
	public function testFormatValueProvider()
	{
		return array(
			array("123\n456", array('cut', "\n"), '123'),
			array("123\n4\r56", array('cut', array("\r", "\n")), '123'),
			array("123\r4\n56", array('cut', array("\r", "\n")), '123'),
			array("123\n456", array('rcut', "\n"), '456'),
			array("123\n4\r56", array('rcut', array("\r", "\n")), '56'),
			array("123\r4\n56", array('rcut', array("\r", "\n")), '56'),
			array('a', array('map', array('a' => 'b', 'c' => 'd')), 'b'),
			array('c', array('map', array('a' => 'b', 'c' => 'd')), 'd'),
			array('a ', array('trim'), 'a'),
			array('a .', array('trim', ' .'), 'a'),
			array('a .', array('trim', '.'), 'a '),
			array('hello', array('callback', function($value) {return $value . ' world';}), 'hello world'),
			array("spam\nham\nni\nhello", array('grep', 'am'), "spam\nham"),
			array("spam\nham\nni\nhello", array('grep', 'world'), ''),
			array('<strong>Spam</strong>: Ham', array('stripTags'), 'Spam: Ham'),
			array('<p>Test paragraph.</p><!-- Comment --> <a href="#fragment">Other text</a>', array('stripTags', '<p><a>'), '<p>Test paragraph.</p> <a href="#fragment">Other text</a>'),
			array('aaa BbB bbb', array('remove', 'bbb'), 'aaa BbB '),
			array('aaa BbB bbb', array('iremove', 'bbb'), 'aaa  '),
			array('aaa BbB bbb ddd Ccc', array('iremove', array('bbb', 'ccc')), 'aaa   ddd '),
			array('aaa BbB bbb', array('replace', 'bbb', 'ccc'), 'aaa BbB ccc'),
			array('aaa BbB bbb', array('ireplace', 'bbb', 'ccc'), 'aaa ccc ccc'),
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
		$formatter = new Formatter();
		$actual = $formatter->formatValue($value, $config);
		$this->assertSame($expected, $actual);
	}
	
	
	public function testFormatValueInsensitiveProvider()
	{
		return array(
			array('ci aaa BbB bbb', array('remove', 'bbb'), 'ci aaa  '),
			array('aaa BbB bbb ddd Ccc', array('remove', array('bbb', 'ccc')), 'aaa   ddd '),
			array('ci aaa BbB bbb', array('replace', 'bbb', 'ccc'), 'ci aaa ccc ccc'),
		);
	}
	
	/**
	 * @dataProvider testFormatValueInsensitiveProvider
	 * @param mixed $value
	 * @param array $config
	 * @param mixed $expected
	 */
	public function testFormatValueInsensitive($value, $config, $expected)
	{
		$formatter = new Formatter();
		$formatter->caseInsensitive = true;
		$actual = $formatter->formatValue($value, $config);
		$this->assertSame($expected, $actual);
	}
	
	
	/**
	 * @expectedException urmaul\formatter\FormatterException
	 */
	public function testMapException()
	{
		$formatter = new Formatter();
		$formatter->formatValue('e', array('map', array('a' => 'b', 'c' => 'd')));
	}
	
	/**
	 * @expectedException urmaul\formatter\FormatterException
	 */
	public function testActionException()
	{
		$formatter = new Formatter();
		$formatter->formatValue('e', array('unknownAction'));
	}
}
