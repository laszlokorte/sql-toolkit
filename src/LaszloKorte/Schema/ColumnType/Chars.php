<?php

namespace LaszloKorte\Schema\ColumnType;

final class Chars implements ColumnType {

	private $length;
	private $varLength;

	public function __construct($length, $varLength) {
		$this->length = $length;
		$this->varLength = $varLength;
	}

	public function getLength() {
		return $this->length;
	}

	public function isOfVariableLength() {
		return $this->varLength;
	}

	public function __toString() {
		return sprintf("%sstring(%d)", !$this->varLength ? 'fixsized ' : '', $this->length);
	}

	public function coerce($value) {
		return (string)($value);
	}

	public function serialize() {
		return serialize([
			$this->length,
			$this->varLength,
		]);
	}

	public function unserialize($data) {
		list(
			$this->length,
			$this->varLength,
		) = unserialize($data);
	}
}