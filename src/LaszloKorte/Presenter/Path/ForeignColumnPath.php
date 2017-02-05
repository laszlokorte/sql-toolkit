<?php

namespace LaszloKorte\Presenter\Path;

use LaszloKorte\Presenter\Identifier;

class ForeignColumnPath implements ColumnPath {
	private $tablePath;
	private $columnName;

	public function __construct(TablePath $tablePath, Identifier $columnName) {
		$this->tablePath = $tablePath;
		$this->columnName = $columnName;
	}

	public function length() {
		return $this->tablePath->length() + 1;
	}

	public function __toString() {
		return "$this->tablePath.$this->columnName";
	}

	public function getTablePath() {
		return $this->tablePath;
	}

	public function getColumnName() {
		return $this->columnName;
	}

	public function relativeTo(TablePath $p) {
		return new ForeignColumnPath($this->tablePath->relativeTo($p), $this->columnName);
	}
}