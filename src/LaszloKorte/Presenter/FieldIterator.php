<?php

namespace LaszloKorte\Presenter;

use Iterator;
use Countable;
use ArrayAccess;

final class FieldIterator implements Iterator, Countable, ArrayAccess {

	private $applicationDef;
	private $entityId;
	private $fieldIds;

	public function __construct($applicationDef, $entityId, $fieldIds) {

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