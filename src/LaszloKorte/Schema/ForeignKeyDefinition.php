<?php

namespace LaszloKorte\Schema;

class ForeignKeyDefinition {
	private $ownTableName;
	private $foreignTableName; 
	private $ownColumns;
	private $foreignColumns;

	public function __construct(Identifier $ownTableName, Identifier $foreignTableName, $ownColumnNames, $foreignColumnNames) {
		if (count($ownColumnNames) !== count($foreignColumnNames)) {
			throw new \Exception("Number of FK Columns do not match.");
		} elseif (count($ownColumnNames) < 1) {
			throw new \Exception("At least one column is required for FK definition");
		}

		$this->ownTableName = $ownTableName;
		$this->foreignTableName = $foreignTableName;
		$this->ownColumns = $ownColumnNames;
		$this->foreignColumns = $foreignColumnNames;
	}

	public function getOwnTableName() {
		return $this->ownTableName;
	}

	public function getForeignTableName() {
		return $this->foreignTableName;
	}

	public function getOwnColumns() {
		return $this->ownColumns;
	}

	public function getForeignColumns() {
		return $this->foreignColumns;
	}

	public function containsOwnColumn($name) {
		return in_array($name, $this->ownColumns);
	}

	public function containsForeignColumn($name) {
		return in_array($name, $this->foreignColumns);
	}
}