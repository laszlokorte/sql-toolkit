<?php

namespace LaszloKorte\Schema;

final class ForeignKeyDefinition {
	const RULE_RESTRICT = 'RESTRICT';
	const RULE_CASCADE = 'CASCADE';
	const RULE_SET_NULL = 'SET_NULL';
	const RULE_NONE = 'NONE';

	private $ownTableName;
	private $foreignTableName; 
	private $ownColumns;
	private $foreignColumns;
	private $onUpdate;
	private $onDelete;

	public function __construct(Identifier $ownTableName, Identifier $foreignTableName, $ownColumnNames, $foreignColumnNames, $onUpdate = self::RULE_RESTRICT, $onDelete = self::RULE_RESTRICT) {
		if (count($ownColumnNames) !== count($foreignColumnNames)) {
			throw new \Exception("Number of FK Columns do not match.");
		} elseif (count($ownColumnNames) < 1) {
			throw new \Exception("At least one column is required for FK definition");
		}

		$allowedStrategies = [
			self::RULE_RESTRICT,
			self::RULE_CASCADE,
			self::RULE_SET_NULL,
			self::RULE_NONE,
		];

		if(!in_array($onUpdate, $allowedStrategies)) {
			throw new \Exception(sprintf("Invalid strategy '%s' for ForeignKey onUpdate. Expected one of (%s)", $onUpdate, implode(', ', $allowedStrategies)));
		}

		if(!in_array($onDelete, $allowedStrategies)) {
			throw new \Exception(sprintf("Invalid strategy '%s' for ForeignKey onDelete. Expected one of (%s)", $onDelete, implode(', ', $allowedStrategies)));
		}

		$this->ownTableName = $ownTableName;
		$this->foreignTableName = $foreignTableName;
		$this->ownColumns = $ownColumnNames;
		$this->foreignColumns = $foreignColumnNames;
		$this->onUpdate = $onUpdate;
		$this->onDelete = $onDelete;
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

	public function getUpdateStrategy() {
		return $this->onUpdate;
	}

	public function getDeleteStrategy() {
		return $this->onDelete;
	}
}