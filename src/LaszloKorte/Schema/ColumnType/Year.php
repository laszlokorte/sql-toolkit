<?php

namespace LaszloKorte\Schema\ColumnType;


class Year implements ColumnType {
	public function __construct($length = 2) {
		$this->length = $length;
	}

	public function __toString() {
		return sprintf("%d digit year", $this->length);
	}

	public function coerce($value) {
		return (string)($value);
	}
}
