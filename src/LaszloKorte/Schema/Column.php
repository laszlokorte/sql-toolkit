<?php

namespace LaszloKorte\Schema;

final class Column {
	private $schemaDefinition;
	private $tableName;
	private $columnName;

	public function __construct(Identifier $columnName, Identifier $tableName, SchemaDefinition $schemaDef) {
		$this->columnName = $columnName;
		$this->tableName = $tableName;
		$this->schemaDefinition = $schemaDef;
	}

	private function def() {
		return $this->tableDef()->getColumnDefinition($this->columnName);
	}

	private function tableDef() {
		return $this->schemaDefinition->getTableDefinition($this->tableName);
	}

	public function belongsToForeignKey() {
		foreach($this->schemaDefinition->getForeignKeyIds($this->tableName) AS $fkid) {
			$fk = $this->schemaDefinition->getForeignKeyDefinition($fkid);
			if($fk->containsOwnColumn($this->columnName)) {
				return true;
			}
		}

		return false;
	}

	public function getForeignKey() {
		foreach($this->schemaDefinition->getForeignKeyIds($this->tableName) AS $fkid) {
			$fk = $this->schemaDefinition->getForeignKeyDefinition($fkid);
			if($fk->containsOwnColumn($this->columnName)) {
				return new ForeignKey($fkid, $this->schemaDefinition);
			}
		}

		return NULL;
	}

	public function getName() {
		return $this->columnName;
	}

	public function getComment() {
		return $this->def()->getComment();
	}

	public function getType() {
		return $this->def()->getType();
	}

	public function isNullable() {
		return $this->def()->isNullable();
	}

	public function getDefaultValue() {
		return $this->def()->getDefaultValue();
	}

	public function __toString() {
		return sprintf("%s", $this->columnName);
	}

	public function belongsToPrimaryKey() {
		return in_array($this->columnName, $this->tableDef()->getPrimaryKeys());
	}

	public function isSerialColumn() {
		return $this->columnName == $this->tableDef()->getSerialColumn();
	}
}