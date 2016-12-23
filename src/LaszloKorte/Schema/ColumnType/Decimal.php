<?php

namespace LaszloKorte\Schema\ColumnType;

class Decimal implements ColumnType {

	public function __construct($totalDigits, $decimalPlaces) {
		$this->totalDigits = $totalDigits;
		$this->decimalPlaces = $decimalPlaces;
	}

	public function __toString() {
		return sprintf('decimal(%d,%d)', $this->totalDigits, $this->decimalPlaces);
	}

	public function coerce($value) {
		return \floatval($value);
	}
}