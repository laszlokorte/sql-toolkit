<?php

namespace LaszloKorte\Schema\ColumnType;

final class Enum implements ColumnType, Enumerable {

	private $multi;
	private $options;

	public function __construct($multi, $options) {
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

	public function allowsMultiple() {
		return $this->multi;
	}

	public function serialize() {
		return serialize([
			$this->multi,
			$this->options,
		]);
	}

	public function unserialize($data) {
		list(
			$this->multi,
			$this->options,
		) = unserialize($data);
	}
}