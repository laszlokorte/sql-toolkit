<?php

namespace LaszloKorte\Schema\ColumnType;


final class Year implements ColumnType {
	private $length;

	public function __construct($length = 2) {
		$this->length = $length;
	}

	public function getLength() {
		return $this->length;
	}

	public function __toString() {
		return sprintf("%d digit year", $this->length);
	}

	public function coerce($value) {
		return (string)($value);
	}

	public function serialize() {
		return serialize([
			$this->length,
		]);
	}

	public function unserialize($data) {
		list(
			$this->length,
		) = unserialize($data);
	}
