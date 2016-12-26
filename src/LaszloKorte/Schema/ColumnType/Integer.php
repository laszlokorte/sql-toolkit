<?php

namespace LaszloKorte\Schema\ColumnType;

class Integer implements ColumnType, Serialable {
	private $unsigned;

	public function __construct($bits, $unsigned) {
		$this->bits = $bits;
		$this->unsigned = $unsigned;
	}

	public function __toString() {
		return sprintf('%s %d bit integer', ($this->unsigned ? 'unsigned' : 'signed'), $this->bits);
	}


	public function coerce($value) {
		return \intval($value);
	}
}