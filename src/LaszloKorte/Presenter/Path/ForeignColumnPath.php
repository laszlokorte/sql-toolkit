<?php

namespace LaszloKorte\Presenter\Path;

class ForeignColumnPath implements Path {
	private $tablePath;

	public function __construct(TablePath $tablePath, $columnName) {
		$this->tablePath = $tablePath;
		$this->columnName = $columnName;
	}

	public function length() {
		return $this->tablePath->length() + 1;
	}

	public function __toString() {
		return "$this->tablePath.$this->columnName";
	}
}