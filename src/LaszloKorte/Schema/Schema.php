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
		$id = new Identifier($name);
		if(!$this->schemaDefinition->hasTableDefinition($id)) {
			throw new \Exception(sprintf('Table "%s" does not exist', $name));
		}
		return new Table($id, $this->schemaDefinition);
	}

	public function getForeignTargetTableName($fkId) {
		return $this->schemaDefinition->getForeignKeyDefinition($fkId)->getForeignTableName();
	}

	public function getOwnTargetTableName($fkId) {
		return $this->schemaDefinition->getForeignKeyDefinition($fkId)->getOwnTableName();
	}
}