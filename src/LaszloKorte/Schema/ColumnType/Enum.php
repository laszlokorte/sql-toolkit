<?php

namespace LaszloKorte\Schema\ColumnType;

class Enum implements ColumnType, Enumerable {
	public function __construct($name, $multi, $options) {
		$this->name = $name;
		$this->multi = $multi;
		$this->options = $options;
	}

	public function __toString() {
		return sprintf("%s(%s)", $this->multi ? 'set' : 'enum', implode(',', $this->options));
	}

	public function coerce($value) {
		return (string)($value);
	}

	public function getOptions() {
		return $this->options;
	}

	public function allowMultiple() {
		return $this->multi;
	}
}