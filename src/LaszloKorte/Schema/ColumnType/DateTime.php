<?php

namespace LaszloKorte\Schema\ColumnType;

class DateTime implements ColumnType {
	public function __toString() {
		return 'datetime';
	}

	public function coerce($value) {
		return (string)($value);
	}
}
