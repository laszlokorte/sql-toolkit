<?php

namespace LaszloKorte\Schema\ColumnType;

class String implements ColumnType {
	public function __construct($length, $varLength) {
		$this->length = $length;
		$this->varLength = $varLength;
	}

	public function __toString() {
		return sprintf("%sstring(%d)", !$this->varLength ? 'fixsized ' : '', $this->length);
	}

	public function coerce($value) {
		return (string)($value);
	}
}