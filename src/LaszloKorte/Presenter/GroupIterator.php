<?php

namespace LaszloKorte\Presenter;

use Iterator;
use Countable;
use ArrayAccess;

final class GroupIterator implements Iterator, Countable, ArrayAccess {

	private $appDef;
	private $groupIds;
	private $pos = 0;

	public function __construct($appDef, $groupIds) {
		$this->appDef = $appDef;
		$this->groupIds = array_values($groupIds);
	}

	public function current() {
		return new Group($this->appDef, $this->groupIds[$this->pos]);
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
		return $this->pos < count($this->groupIds);
	}

	public function count() {
		return count($this->groupIds);
	}

	public function offsetExists($offset) {
		return $offset > 0 && $offset < count($this->groupIds);
	}

	public function offsetGet($offset) {
		return new Group($this->appDef, $this->groupIds[$offset]);
	}

	public function offsetSet($offset, $value) {

	}

	public function offsetUnset($offset) {

	}
}