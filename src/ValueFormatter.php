<?php

namespace urmaul\formatter;

/**
 * A class that formats single value.
 */
class ValueFormatter extends Formatter
{
	public function format($value, $rules = null)
	{
		if ($rules === null)
			$rules = $this->rules;
		
		foreach ($rules as $rule) {
			$value = $this->formatValue($value, $rule);
		}
		
		return $value;
	}
}
