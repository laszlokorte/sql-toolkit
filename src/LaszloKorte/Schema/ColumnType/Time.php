<?php

namespace LaszloKorte\Schema\ColumnType;

class Time implements ColumnType {
	public function __toString() {
		return 'time';
	}

	public function coerce($value) {
		return (string)($value);
	}
}