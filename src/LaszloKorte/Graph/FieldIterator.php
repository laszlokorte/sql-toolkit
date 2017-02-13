<?php

namespace LaszloKorte\Graph;

use Iterator;
use Countable;
use ArrayAccess;

final class FieldIterator implements Iterator, Countable, ArrayAccess {

	private $graphDef;
	private $entityId;
	private $fieldIds;
	private $pos = 0;

	public function __construct($graphDef, $entityId, $fieldIds) {
		$this->graphDef = $graphDef;
		$this->entityId = $entityId;
		$this->fieldIds = $fieldIds;
	}

	public function current() {
		return new Field($this->graphDef, $this->entityId, $this->fieldIds[$this->pos]);
	}

	public function key() {
		return $this->pos;
	}

	public function next() {
		++$this->pos;
	}

	public function rewind() {
		$this->pos = 0;
	}

	public function valid() {
		return $this->pos < count($this->fieldIds);
	}

	public function count() {
		return count($this->fieldIds);
	}

	public function offsetExists($offset) {
		return $offset >= 0 && $offset < count($this->fieldIds);
	}

	public function offsetGet($offset) {
		return new Field($this->graphDef, $this->entityId, $this->fieldIds[$offset]);
	}

	public function offsetSet($offset, $value) {

	}

	public function offsetUnset($offset) {

	}
}