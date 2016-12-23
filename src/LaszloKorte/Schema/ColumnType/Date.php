<?php

namespace LaszloKorte\Schema\ColumnType;

class Date implements ColumnType {
	public function __toString() {
		return 'date';
	}

	public function coerce($value) {
		return (string)($value);
	}
}