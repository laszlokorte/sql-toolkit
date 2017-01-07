<?php

namespace LaszloKorte\Schema\ColumnType;

final class DateTime implements ColumnType {
	public function __toString() {
		return 'datetime';
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
