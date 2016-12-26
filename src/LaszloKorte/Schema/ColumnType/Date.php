<?php

namespace LaszloKorte\Schema\ColumnType;

final class Date implements ColumnType {
	public function __toString() {
		return 'date';
	}

	public function coerce($value) {
		return (string)($value);
	}
}