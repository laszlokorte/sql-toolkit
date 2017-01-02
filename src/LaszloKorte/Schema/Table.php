<?php

namespace LaszloKorte\Schema;

final class Table {
	private $schemaDefinition;
	private $tableName;

	public function __construct(Identifier $tableName, SchemaDefinition $schemaDef) {
		$this->tableName = $tableName;
		$this->schemaDefinition = $schemaDef;
	}

	public function columns($includeForeignKeys = TRUE) {
		if($includeForeignKeys) {
			$columnIds = $this->def()->getColumnIds();
		} else {
			$columnIds = array_values(array_filter($this->def()->getColumnIds(), function($colId) {
				return !$this->schemaDefinition->columnBelongsToForeignKey($this->tableName, $colId);
			}));
		}

		return new ColumnsIterator($this->schemaDefinition, $this->tableName, $columnIds);
	}

	public function foreignKeys() {
		return new ForeignKeysIterator($this->schemaDefinition, $this->schemaDefinition->getForeignKeyIds($this->tableName));
	}

	public function reverseforeignKeys() {
		return new ForeignKeysIterator($this->schemaDefinition, $this->schemaDefinition->getReverseForeignKeyIds($this->tableName));
	}

	public function indices() {
		return new IndicesIterator($this->schemaDefinition, $this->tableName, $this->def()->getIndices());
	}

	public function column($name) {
		return new Column(new Identifier($name), $this->tableName, $this->schemaDefinition);
	}

	public function foreignKey($name) {
		return new ForeignKey(new Identifier($name), $this->tableName, $this->schemaDefinition);
	}

	public function index($name) {
		return new Index(new Identifier($name), $this->tableName, $this->schemaDefinition);
	}

	public function getName() {
		return $this->tableName;
	}

	public function getComment() {
		return $this->def()->getComment();
	}

	public function __toString() {
		return sprintf("%s", $this->tableName);
	}

	public function hasSerialColumn() {
		return $this->def()->getSerialColumn() !== NULL;
	}

	public function getSerialColumn() {
		return $this->def()->getSerialColumn();
	}

	public function primaryKeys() {
		return new ColumnsIterator($this->schemaDefinition, $this->tableName, $this->def()->getPrimaryKeys());
	}

	public function hasPrimaryKeys() {
		return count($this->def()->getPrimaryKeys()) !== 0;
	}

	private function def() {
		return $this->schemaDefinition->getTableDefinition($this->tableName);
	}
}