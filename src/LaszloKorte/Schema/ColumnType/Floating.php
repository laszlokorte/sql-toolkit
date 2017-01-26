<?php

namespace LaszloKorte\Schema\ColumnType;

final class Floating implements ColumnType, Serialable {

	private $bits;

	public function __construct($bits) {
		$this->bits = $bits;
	}

	public function getBitCount() {
		return $this->bits;
	}

	public function __toString() {
		return sprintf('%d bits floating point', $this->bits);
	}

	public function coerce($value) {
		return \floatval($value);
	}

	public function serialize() {
		return serialize([
			$this->bits,
		]);
	}

	public function unserialize($data) {
		list(
			$this->bits,
		) = unserialize($data);
	}
}