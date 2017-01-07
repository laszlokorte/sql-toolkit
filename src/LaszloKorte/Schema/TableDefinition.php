<?php

namespace LaszloKorte\Schema;

use LaszloKorte\Schema\ColumnType\ColumnType;
use LaszloKorte\Schema\ColumnType\Serialable;

use Serializable;

final class TableDefinition implements Serializable {
	private $columnDefinitions;
	private $comment;

	private $primaryKeyColumns = [];
	private $serialColumn = NULL;
	private $indices;

	public function __construct($comment) {
		$this->columnDefinitions = new IdentifierMap();
		$this->foreignKeys = new IdentifierMap();
		$this->indices = new IdentifierMap();
		$this->comment = $comment;
	}

	public function getPrimaryKeys() {
		return $this->primaryKeyColumns;
	}

	public function getSerialColumn() {
		return $this->serialColumn;
	}

	public function defineSerial($columnName) {
		if(!isset($this->columnDefinitions[$columnName])) {
			throw new Exception(sprintf('Column %s is not defined in this table.', $columnName));
		}
		$col = $this->columnDefinitions[$columnName];

		if(!$col->getType() instanceof Serialable) {
			throw new \Exception(sprintf('Column %s is of type %s and can not be a serial column', $colName, $col->getType()));
		}

		$this->serialColumn = $columnName;
	}

	public function definePrimaryKey($columnNames) {
		if(!is_array($columnNames)) {
			$columnNames = [$columnNames];
		}

		foreach($columnNames AS $col) {
			if (!$col instanceof Identifier) {
				throw new \Exception(sprintf('$columnName is expected to be an %s'), Identifier::class);
			}
			if(!isset($this->columnDefinitions[$col])) {
				throw new \Exception(sprintf('Column %s is not defined in this table.', $col));
			}
		}

		$this->primaryKeyColumns = $columnNames;
	}

	public function defineColumn(Identifier $name, ColumnType $type, $allowNull = FALSE, $defaultValue = NULL, $comment) {
		if ($this->columnDefinitions->offsetExists($name)) {
			throw new \Exception(sprintf('Column with name \'%s\' is already defined.', $name));
		}

		$column = new ColumnDefinition($type, $allowNull, $defaultValue, $comment);
		$this->columnDefinitions[$name] = $column;

		return $column;
	}

	public function defineIndex($type, Identifier $indexName, $columnNames) {
		if ($this->indices->offsetExists($indexName)) {
			throw new \Exception(sprintf('Index with name \'%s\' is already defined.', $indexName));
		}
		foreach($columnNames AS $col) {
			if (!$col instanceof Identifier) {
				throw new \Exception(sprintf('$columnName is expected to be an %s'), Identifier::class);
			}
			if (!isset($this->columnDefinitions[$col])) {
				throw new Exception(sprintf('Column %s is not defined in this table.', $col));
			}
		}

		$index = new IndexDefinition($type, $columnNames);
		$this->indices[$indexName] = $index;

		return $index;
	}

	public function getColumnDefinition(Identifier $name) {
		return $this->columnDefinitions[$name];
	}

	public function hasColumnDefinition(Identifier $name) {
		return isset($this->columnDefinitions[$name]);
	}

	public function getIndexDefinition(Identifier $name) {
		return $this->indices[$name];
	}

	public function getColumnIds() {
		$result = [];
		foreach($this->columnDefinitions AS $id) {
			$result []= $id;
		}
		return $result;
	}

	public function getIndices() {
		$result = [];
		foreach($this->indices AS $id) {
			$result []= $id;
		}
		return $result;
	}

	public function getComment() {
		return $this->comment;
	}

	public function serialize() {
		return serialize([
			$this->columnDefinitions,
			$this->comment,
			$this->primaryKeyColumns,
			$this->serialColumn,
			$this->indices,
		]);
	}

	public function unserialize($data) {
		list(
			$this->columnDefinitions,
			$this->comment,
			$this->primaryKeyColumns,
			$this->serialColumn,
			$this->indices
		) = unserialize($data);
	}
}