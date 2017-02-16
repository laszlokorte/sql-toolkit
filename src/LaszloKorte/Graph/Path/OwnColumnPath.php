<?php

namespace LaszloKorte\Graph\Path;

use LaszloKorte\Graph\Identifier;

class OwnColumnPath implements ColumnPath {
	private $tableName;
	private $columnName;

	public function __construct(Identifier $tableName, Identifier $columnName) {
		$this->tableName = $tableName;
		$this->columnName = $columnName;
	}

	public function length() {
		return 1;
	}

	public function __toString() {
		return "self.$this->columnName";
	}

	public function relativeTo(TablePath $p) {
		return new ForeignColumnPath($p, $this->columnName);
	}

	public function getTableName() {
		return $this->tableName;
	}

	public function getColumnName() {
		return $this->columnName;
	}
}