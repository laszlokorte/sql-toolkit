<?php

namespace LaszloKorte\Presenter;

use Iterator;
use Countable;
use ArrayAccess;

final class EntityIterator implements Iterator, Countable, ArrayAccess {
	private $applicationDef;
	private $entityIds;

	public function __construct($applicationDef, $entityIds) {

	}

	public function count() {
	
	}

	public function offsetExists($offset) {
	}

	public function offsetGet($offset) {
	}

	public function offsetSet($offset, $value) {

	}

	public function offsetUnset($offset) {

	}
}