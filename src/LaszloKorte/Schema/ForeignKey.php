<?php

namespace LaszloKorte\Schema;

final class ForeignKey {
	const RULE_RESTRICT = ForeignKeyDefinition::RULE_RESTRICT;
	const RULE_CASCADE = ForeignKeyDefinition::RULE_CASCADE;
	const RULE_SET_NULL = ForeignKeyDefinition::RULE_SET_NULL;
	const RULE_NONE = ForeignKeyDefinition::RULE_NONE;

	private $schemaDefinition;
	private $keyName;

	public function __construct(Identifier $keyName, SchemaDefinition $schemaDef) {
		$this->keyName = $keyName;
		$this->schemaDefinition = $schemaDef;
	}

	private function def() {
		return $this->schemaDefinition->getForeignKeyDefinition($this->keyName);
	}

	public function getOwnColumns() {
		$def = $this->def();
		return new ColumnsIterator($this->schemaDefinition, $def->getOwnTableName(), $def->getOwnColumns());
	}

	public function getForeignColumns() {
		$def = $this->def();
		return new ColumnsIterator($this->schemaDefinition, $def->getForeignTableName(), $def->getForeignColumns());
	}

	public function getOwnTable() {
		return new Table($this->def()->getOwnTableName(), $this->schemaDefinition);
	}

	public function getTargetTable() {
		return new Table($this->def()->getForeignTableName(), $this->schemaDefinition);
	}

	public function getName() {
		return $this->keyName;
	}

	public function isRequired() {
		foreach($this->getOwnColumns() AS $col) {
			if($col->isNullable()) {
				return FALSE;
			}
		}

		return true;
	}

	public function __toString() {
		$def = $this->def();

		return sprintf("%s [%s->%s] (%s,%s)", $this->keyName, $def->getOwnTableName(), $def->getForeignTableName(), $def->getUpdateStrategy(), $def->getDeleteStrategy());
	}
}