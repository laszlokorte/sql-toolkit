<?php

namespace LaszloKorte\Graph\Path;

use LaszloKorte\Graph\Identifier;

use Serializable;

class ForeignColumnPath implements ColumnPath, Serializable {
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

	public function serialize() {
		return serialize([
			$this->tablePath,
			$this->columnName,
		]);
	}

	public function unserialize($data) {
		list(
			$this->tablePath,
			$this->columnName,
		) = unserialize($data);
	}
}