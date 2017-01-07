<?php

namespace LaszloKorte\Schema\ColumnType;

final class Enum implements ColumnType, Enumerable {
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

	public function serialize() {
		return serialize([
			$this->name,
			$this->multi,
			$this->options,
		]);
	}

	public function unserialize($data) {
		list(
			$this->name,
			$this->multi,
			$this->options,
		) = unserialize($data);
	}
}