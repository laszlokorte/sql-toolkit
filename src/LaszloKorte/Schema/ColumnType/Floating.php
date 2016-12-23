<?php

namespace LaszloKorte\Schema\ColumnType;

class Floating implements ColumnType, Serialable {

	public function __construct($bits) {
		$this->bits = $bits;
	}

	public function __toString() {
		return sprintf('%d bits floating point', $this->bits);
	}

	public function coerce($value) {
		return \floatval($value);
	}
}