<?php

namespace LaszloKorte\Schema\ColumnType;

final class TimeStamp implements ColumnType {
	public function __toString() {
		return 'timestamp';
	}

	public function coerce($value) {
		return (string)($value);
	}

	public function serialize() {
		return serialize([]);
	}

	public function unserialize($data) {
		
	}
}