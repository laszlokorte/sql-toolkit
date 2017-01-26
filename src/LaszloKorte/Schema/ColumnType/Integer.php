<?php

namespace LaszloKorte\Schema\ColumnType;

final class Integer implements ColumnType, Serialable {
	private $bits;
	private $unsigned;

	public function __construct($bits, $unsigned) {
		$this->bits = $bits;
		$this->unsigned = $unsigned;
	}

	public function getBitCount() {
		return $this->bits;
	}

	public function isSigned() {
		return !$this->unsigned;
	}

	public function __toString() {
		return sprintf('%s %d bit integer', ($this->unsigned ? 'unsigned' : 'signed'), $this->bits);
	}


	public function coerce($value) {
		return \intval($value);
	}

	public function serialize() {
		return serialize([
			$this->bits,
			$this->unsigned,
		]);
	}

	public function unserialize($data) {
		list(
			$this->bits,
			$this->unsigned,
		) = unserialize($data);
	}
}
