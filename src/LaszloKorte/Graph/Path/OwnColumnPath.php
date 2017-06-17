<?php

namespace LaszloKorte\Graph\Path;

use LaszloKorte\Graph\Identifier;

use Serializable;

class OwnColumnPath implements ColumnPath, Serializable {
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

	public function serialize() {
		return serialize([
			$this->tableName,
			$this->columnName,
		]);
	}

	public function unserialize($data) {
		list(
			$this->tableName,
			$this->columnName,
		) = unserialize($data);
	}
}