<?php

namespace LaszloKorte\Schema\ColumnType;

class TimeStamp implements ColumnType {
	public function __toString() {
		return 'timestamp';
	}

	public function coerce($value) {
		return (string)($value);
	}
}