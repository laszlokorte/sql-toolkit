<?php

namespace LaszloKorte\Schema\ColumnType;

final class Blob implements ColumnType {
	public function __construct($length, $binary) {
		$this->length = $length;
		$this->binary = $binary;
	}

	public function __toString() {
		return sprintf("2^%d byte %s", $this->length, $this->binary ? 'blob' : 'text');
	}

	public function coerce($value) {
		return (string)($value);
	}
}