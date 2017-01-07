<?php

namespace LaszloKorte\Schema\ColumnType;

final class Time implements ColumnType {
	public function __toString() {
		return 'time';
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