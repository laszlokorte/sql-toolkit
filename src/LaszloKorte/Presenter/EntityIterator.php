<?php

namespace LaszloKorte\Presenter;

use Iterator;
use Countable;
use ArrayAccess;

final class EntityIterator implements Iterator, Countable, ArrayAccess {
	private $appDef;
	private $entityIds;
	private $pos = 0;

	public function __construct($appDef, $entityIds) {
		$this->appDef = $appDef;
		$this->entityIds = array_values($entityIds);
	}

	public function current() {
		return new Entity($this->appDef, $this->entityIds[$this->pos]);
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
		return $this->pos < count($this->entityIds);
	}

	public function count() {
		return count($this->entityIds);
	}

	public function offsetExists($offset) {
		return $offset >= 0 && $offset < count($this->entityIds);
	}

	public function offsetGet($offset) {
		return new Entity($this->appDef, $this->entityIds[$offset]);
	}

	public function offsetSet($offset, $value) {

	}

	public function offsetUnset($offset) {

	}
}