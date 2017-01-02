<?php

namespace LaszloKorte\Schema;

use Iterator;
use Countable;
use ArrayAccess;

final class ColumnsIterator implements Iterator, Countable, ArrayAccess {

	private $schemaDefinition;
	private $tableName;
	private $columnNames;

	public function __construct(SchemaDefinition $schemaDefinition, Identifier $tableName, array $columnNames) {
		$this->schemaDefinition = $schemaDefinition;
		$this->columnNames = $columnNames;
		$this->tableName = $tableName;
	}

	public function current() {
		return new Column($this->columnNames[$this->position], $this->tableName, $this->schemaDefinition);
	}

	public function key() {
		return $this->position;
	}

	public function next() {
		++$this->position;
	}

	public function rewind() {
		$this->position = 0;
	}

	public function valid() {
		return $this->position >= 0 && $this->position < count($this->columnNames);
	}

	public function count() {
		return count($this->columnNames);
	}

	public function offsetExists($offset) {
		return is_int($offset) && $offset >= 0 && $offset < count($this->columnNames);
	}

	public function offsetGet($offset) {
		return new Column($this->columnNames[$offset], $this->tableName, $this->schemaDefinition);
	}

	public function offsetSet($offset, $value) {

	}

	public function offsetUnset($offset) {

	}

}