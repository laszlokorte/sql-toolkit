<?php

namespace LaszloKorte\Schema;

use Iterator;
use Countable;
use ArrayAccess;

final class TablesIterator implements Iterator, Countable, ArrayAccess {
	
	private $schemaDefinition;
	private $tableNames;
	private $position = 0;

	public function __construct(SchemaDefinition $schemaDefinition, array $tableNames) {
		$this->schemaDefinition = $schemaDefinition;
		$this->tableNames = $tableNames;
	}

	public function current() {
		return new Table($this->tableNames[$this->position], $this->schemaDefinition);
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
		return $this->position < count($this->tableNames);
	}

	public function count() {
		return count($this->tableNames);
	}

	public function offsetExists($offset) {
		return is_int($offset) && $offset >= 0 && $offset < count($this->tableNames);
	}

	public function offsetGet($offset) {
		return new Table($this->tableNames[$offset], $this->schemaDefinition);
	}

	public function offsetSet($offset, $value) {

	}

	public function offsetUnset($offset) {

	}

	public function contains($name) {
		return in_array(new Identifier($name), $this->tableNames);
	}
}