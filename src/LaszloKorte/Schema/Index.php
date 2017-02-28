<?php

namespace LaszloKorte\Schema;

final class Index {
	const TYPE_UNIQUE = IndexDefinition::TYPE_UNIQUE;
	const TYPE_KEY = IndexDefinition::TYPE_KEY;
	const TYPE_FULLTEXT = IndexDefinition::TYPE_FULLTEXT;

	private $schemaDefinition;
	private $tableName;
	private $indexName;

	public function __construct($indexName, $tableName, $schemaDefinition) {
		$this->schemaDefinition = $schemaDefinition;
		$this->tableName = $tableName;
		$this->indexName = $indexName;
	}

	private function def() {
		return $this->schemaDefinition->getTableDefinition($this->tableName)->getIndexDefinition($this->indexName);
	}

	public function isUnique() {
		return $this->def()->getType() === IndexDefinition::TYPE_UNIQUE;
	}

	public function isFulltext() {
		return $this->def()->getType() === IndexDefinition::TYPE_FULLTEXT;
	}

	public function getColumns() {
		return new ColumnsIterator($this->schemaDefinition, $this->tableName, $this->def()->getColumnNames());
	}

	public function __toString() {
		$def = $this->def();

		return sprintf("%s[%s] (%s)", $this->indexName, $def->getType(), implode(', ', $def->getColumnNames()));
	}
}