<?php

namespace LaszloKorte\Schema;

use LaszloKorte\Schema\ColumnType\ColumnType;
use LaszloKorte\Schema\ColumnType\Serialable;

final class SchemaDefinition {
	private $tableDefinitions;
	private $foreignKeys;

	public function __construct() {
		$this->tableDefinitions = new IdentifierMap();
		$this->foreignKeys = new IdentifierMap();
	}

	public function defineTable(Identifier $name, $comment = '') {
		if (isset($this->tableDefinitions[$name])) {
			throw new \Exception(sprintf('Table with name \'%s\' is already defined.', $name));
		}

		$table = new TableDefinition($comment);
		$this->tableDefinitions[$name] = $table;

		return $table;
	}

	public function hasTableDefinition(Identifier $name) {
		return isset($this->tableDefinitions[$name]);
	}

	public function getTableDefinition(Identifier $name) {
		return $this->tableDefinitions[$name];
	}

	public function defineForeignKey(Identifier $name, Identifier $ownTable, Identifier $foreignTable, $ownColumnNames, $foreignColumnNames) {
		if($this->foreignKeys->offsetExists($name)) {
			throw new \Exception(sprintf('FK with name \'%s\' is already defined.', $name));
		}

		$fk = new ForeignKeyDefinition($ownTable, $foreignTable, $ownColumnNames, $foreignColumnNames);

		$this->foreignKeys[$name] = $fk;

		return $fk;
	}

	public function getTableIds() {
		$result = [];
		foreach($this->tableDefinitions AS $id) {
			$result []= $id;
		}
		return $result;
	}

	public function getForeignKeyDefinition(Identifier $name) {
		return $this->foreignKeys[$name];
	}

	public function getForeignKeyIds($tableName) {
		$result = [];
		foreach($this->foreignKeys AS $id) {
			if($this->foreignKeys[$id]->getOwnTableName() != $tableName) {
				continue;
			}
			$result []= $id;
		}
		return $result;
	}

	public function getReverseForeignKeyIds($tableName) {
		$result = [];
		foreach($this->foreignKeys AS $id) {
			if($this->foreignKeys[$id]->getForeignTableName() != $tableName) {
				continue;
			}
			$result []= $id;
		}
		return $result;
	}

	public function columnBelongsToForeignKey(Identifier $tableName, Identifier $columnName) {
		foreach($this->getForeignKeyIds($tableName) AS $fkid) {
			$fk = $this->getForeignKeyDefinition($fkid);
			if($fk->containsOwnColumn($columnName)) {
				return true;
			}
		}

		return false;
	}
}