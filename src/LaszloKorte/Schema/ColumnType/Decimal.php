<?php

namespace LaszloKorte\Schema\ColumnType;

final class Decimal implements ColumnType {

	private $totalDigits;
	private $decimalPlaces;

	public function __construct($totalDigits, $decimalPlaces) {
		$this->totalDigits = $totalDigits;
		$this->decimalPlaces = $decimalPlaces;
	}

	public function getTotalDigits() {
		return $this->totalDigits;
	}

	public function getDecimalPlaces() {
		return $this->decimalPlaces;
	}

	public function getIntegerPlaces() {
		return $this->totalDigits - $this->decimalPlaces;
	}

	public function __toString() {
		return sprintf('decimal(%d,%d)', $this->totalDigits, $this->decimalPlaces);
	}

	public function coerce($value) {
		return \floatval($value);
	}

	public function serialize() {
		return serialize([
			$this->totalDigits,
			$this->decimalPlaces,
		]);
	}

	public function unserialize($data) {
		list(
			$this->totalDigits,
			$this->decimalPlaces
		) = unserialize($data);
	}
}