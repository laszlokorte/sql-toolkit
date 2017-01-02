<?php

namespace LaszloKorte\Schema;

final class Schema {
	private $schemaDefinition;

	public function __construct(SchemaDefinition $schemaDefinition) {
		$this->schemaDefinition = $schemaDefinition;
	}

	public function tables() {
		return new TablesIterator($this->schemaDefinition, $this->schemaDefinition->getTableIds());
	}

	public function table($name) {
		return new Table(new Identifier($name), $this->schemaDefinition);
	}

	public function getForeignTargetTableName($fkId) {
		return $this->schemaDefinition->getForeignKeyDefinition($fkId)->getForeignTableName();
	}

	public function getOwnTargetTableName($fkId) {
		return $this->schemaDefinition->getForeignKeyDefinition($fkId)->getOwnTableName();
	}
}