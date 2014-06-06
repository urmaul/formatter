# Formatter

Rule-based string format.

[![Build Status](https://travis-ci.org/urmaul/url.svg)](https://travis-ci.org/urmaul/formatter)

## Installing

``
composer require urmaul/formatter dev-master
``

## Actions

* **cut(delimiter)** - returns string part between beginning and delimiter.
* **rcut(delimiter)** - returns string part between delimiter and ending.
* **remove(substring)** - removes substring. See [str_replace](http://www.php.net/manual/en/function.str-replace.php).
* **replace(substring, replacement)** - replaces substring with replacement. See [str_replace](http://www.php.net/manual/en/function.str-replace.php).
* **removeMatch(pattern)** - removes pattern. See [preg_replace](http://www.php.net/manual/en/function.preg-replace.php).
* **replaceMatch(pattern, replacement)** - replaces pattern with replacement. See [preg_replace](http://www.php.net/manual/en/function.preg-replace.php).
* **map(array map)** - replaces string with value from map.
* **between(leftBorder, rightBorder)** - returns string part between first occurence of leftBorder and rightBorder.
* **trim([character_mask = " \t\n\r\0\x0B"])** - strips whitespace (or other characters) from the beginning and end of a string.
* **callback(callable)** - processes string with callback.
* **grep(substring)** - returns lines containing substring.
* **stripTags([allowable_tags])** - strips HTML and PHP tags from a string. See [strip_tags](http://www.php.net/manual/en/function.strip-tags.php).
