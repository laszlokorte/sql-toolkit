<?php

namespace LaszloKorte\Schema;

class Table {
	private $schemaDefinition;
	private $tableName;

	public function __construct(Identifier $tableName, SchemaDefinition $schemaDef) {
		$this->tableName = $tableName;
		$this->schemaDefinition = $schemaDef;
	}

	public function columns($includeForeignKeys = TRUE) {
		$cols = array_map(function($id) {
			return new Column($id, $this->tableName, $this->schemaDefinition);
		}, $this->def()->getColumnIds());

		if($includeForeignKeys) {
			return $cols;
		} else {
			return array_filter($cols, function($c) {
				return !$c->belongsToForeignKey();
			});
		}
	}

	public function foreignKeys() {
		return array_map(function($id) {
			return new ForeignKey($id, $this->schemaDefinition);
		}, $this->schemaDefinition->getForeignKeyIds($this->tableName));
	}

	public function reverseforeignKeys() {
		return array_map(function($id) {
			return new ForeignKey($id, $this->schemaDefinition);
		}, $this->schemaDefinition->getReverseForeignKeyIds($this->tableName));
	}

	public function indices() {
		return array_map(function($indexName) {
			return new Index($indexName, $this->tableName, $this->schemaDefinition);
		}, $this->def()->getIndices());
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
		return array_map(function($colName) {
			return new Column($colName, $this->tableName, $this->schemaDefinition);
		}, $this->def()->getPrimaryKeys());
	}

	public function hasPrimaryKeys() {
		return count($this->def()->getPrimaryKeys()) !== 0;
	}

	private function def() {
		return $this->schemaDefinition->getTableDefinition($this->tableName);
	}
}