<?php

namespace LaszloKorte\Schema\ColumnType;

final class Blob implements ColumnType {
	private $length;
	private $binary;

	public function __construct($length, $binary) {
		$this->length = $length;
		$this->binary = $binary;
	}

	public function getLength() {
		return $this->length;
	}

	public function isBinary() {
		return $this->binary;
	}

	public function __toString() {
		return sprintf("2^%d byte %s", $this->length, $this->binary ? 'blob' : 'text');
	}

	public function coerce($value) {
		return (string)($value);
	}

	public function serialize() {
		return serialize([
			$this->length,
			$this->binary,
		]);
	}

	public function unserialize($data) {
		list(
			$this->length,
			$this->binary,
		) = unserialize($data);
	}
}