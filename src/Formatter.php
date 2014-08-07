<?php

namespace urmaul\formatter;

class Formatter
{
	/**
	 * @var array
	 */
	public $rules;
	
	/**
	 * If true - all actons are case insensitive
	 * @var boolean 
	 */
	public $caseInsensitive = false;
	
	public function __construct($rules = array())
	{
		$this->rules = $rules;
	}
	
	public function format($values, $rules = null)
	{
		if ($rules === null)
			$rules = $this->rules;
		
		foreach ($rules as $rule) {
			$attributesStr = array_shift($rule);
			$attributes = explode(',', str_replace(' ', '', $attributesStr));
			foreach ($attributes as $attribute) {
				if (array_key_exists($attribute, $values)) {
					$value = &$values[$attribute];
					$value = $this->formatValue($value, $rule);
				}
			}
		}
		
		return $values;
	}
	
	public function formatValue($value, $rule)
	{
		$action = array_shift($rule);
		switch ($action) {
			case 'cut':
				return $this->cut($value, $rule[0]);
				
			case 'rcut':
				return $this->cut($value, $rule[0], true);
			
			case 'remove':
				if (!$this->caseInsensitive)
					return str_replace($rule[0], '', $value);
			
				// Otherwise fallthrough
			case 'iremove':
				$pattern = sprintf('/%s/iu', preg_quote($rule[0], '/'));
				return preg_replace($pattern, '', $value);
				
			case 'replace':
				if (!$this->caseInsensitive)
					return str_replace($rule[0], $rule[1], $value);
				
				// Otherwise fallthrough
			case 'ireplace':
				$pattern = sprintf('/%s/iu', preg_quote($rule[0], '/'));
				return preg_replace($pattern, preg_quote($rule[1]), $value);
			
			case 'removeMatch':
				return preg_replace($rule[0], '', $value);

			case 'replaceMatch':
				return preg_replace($rule[0], $rule[1], $value);

			case 'map':
				if (isset($rule[0][$value]))
					return $rule[0][$value];
				else
					throw new FormatterException('Unknown map value: "' . $value . '"');
			
			case 'between':
				return $this->between($value, $rule[0], $rule[1]);
				
			case 'trim':
				$chars = isset($rule[0]) ? $rule[0] : "  \t\n\r\0\x0B"; // Some utf8 spaces added
				return trim($value, $chars);
				
			case 'callback':
				return call_user_func($rule[0], $value);

			case 'grep':
				$lines = explode("\n", $value);
				$lines = array_filter($lines, function($line)use($rule){return strpos($line, $rule[0]) !== false;});
				return implode("\n", $lines);

			case 'stripTags':
				if (isset($rule[0]))
					return strip_tags($value, $rule[0]);
				else
					return strip_tags($value);

			default:
				throw new FormatterException('Unknown formatter action: "' . $action . '"');
		}
	}
	
	public function cut($text, $delimiters, $right = false)
	{
		if (!is_array($delimiters))
			$delimiters = array($delimiters);
		
		foreach ($delimiters as $delimiter) {
			$pos = mb_strpos($text, $delimiter);
			if ($pos !== false) {
				if ($right)
					$text = mb_substr($text, $pos + mb_strlen($delimiter));
				else
					$text = mb_substr($text, 0 , $pos);
			}
		}

		return $text;
	}
	
	/**
	 * Returns string part between first occurence of $leftBorder and $rightBorder.
	 * @param string $text text to look inside.
	 * @param string $leftBorder left border substring.
	 * @param string $rightBorder right border substring. End of file if not
	 * defined.
	 * @return string|boolean found text or false
	 */
	public static function between($text, $leftBorder, $rightBorder = null)
	{
		$offset = stripos($text, $leftBorder);
		if ($offset === false)
			return false;
		$offset += strlen($leftBorder);
		if ($rightBorder === null)
			return substr($text, $offset);
		$offset2 = stripos($text, $rightBorder, $offset);
		if ($offset2 === false)
			return false;
		return substr($text, $offset, $offset2-$offset);
	}
}
