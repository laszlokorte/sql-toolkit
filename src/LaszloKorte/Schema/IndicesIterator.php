<?php

namespace LaszloKorte\Schema;

use Iterator;
use Countable;
use ArrayAccess;

final class IndicesIterator implements Iterator, Countable, ArrayAccess {

	private $schemaDefinition;
	private $tableName;
	private $indexNames;

	public function __construct(SchemaDefinition $schemaDefinition, Identifier $tableName, array $indexNames) {
		$this->schemaDefinition = $schemaDefinition;
		$this->indexNames = $indexNames;
		$this->tableName = $tableName;
	}

	public function current() {
		return new Index($this->indexNames[$this->position], $this->tableName, $this->schemaDefinition);
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
		return $this->position >= 0 && $this->position < count($this->indexNames);
	}

	public function count() {
		return count($this->indexNames);
	}

	public function offsetExists($offset) {
		return is_int($offset) && $offset >= 0 && $offset < count($this->indexNames);
	}

	public function offsetGet($offset) {
		return new Index($this->indexNames[$offset], $this->tableName, $this->schemaDefinition);
	}

	public function offsetSet($offset, $value) {

	}

	public function offsetUnset($offset) {

	}

}