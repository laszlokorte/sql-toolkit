<?php

namespace LaszloKorte\Schema;

use Iterator;
use Countable;
use ArrayAccess;

final class ForeignKeysIterator implements Iterator, Countable, ArrayAccess {

	private $schemaDefinition;
	private $fkNames;

	public function __construct(SchemaDefinition $schemaDefinition, array $fkNames) {
		$this->schemaDefinition = $schemaDefinition;
		$this->fkNames = $fkNames;
	}

	public function current() {
		return new ForeignKey($this->fkNames[$this->position], $this->schemaDefinition);
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
		return $this->position >= 0 && $this->position < count($this->fkNames);
	}

	public function count() {
		return count($this->fkNames);
	}

	public function offsetExists($offset) {
		return is_int($offset) && $offset >= 0 && $offset < count($this->fkNames);
	}

	public function offsetGet($offset) {
		return new ForeignKey($this->fkNames[$offset], $this->schemaDefinition);
	}

	public function offsetSet($offset, $value) {

	}

	public function offsetUnset($offset) {

	}

	public function contains($name) {
		return in_array(new Identifier($name), $this->fkNames);
	}

	public function getByName($name) {
		$id = new Identifier($name);
		if(!in_array($id, $this->fkNames)) {
			throw new \Exception(sprintf("FK does not exist: %s", $name));
		}
		return new ForeignKey($id, $this->schemaDefinition);
	}

}