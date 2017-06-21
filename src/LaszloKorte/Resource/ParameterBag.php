<?php

namespace LaszloKorte\Resource;

use ArrayAccess;

final class ParameterBag implements ArrayAccess {

	private $values;
	private $parent;
	
	public function __construct(array $values = [], $parent = NULL) {
		$this->values = array_map([$this, 'scalarize'], $values);
		$this->parent = $parent;
	}

	public function replace($key, $value) {
		$reversePath = array_map([$this, 'scalarize'], array_reverse(
			is_array($key) ? $key : explode('.', $key)
		));
		$newValue = array_reduce($reversePath, function($v, $currentKey) {
			return [$currentKey => $v];
		}, $this->scalarize($value));

		return new ParameterBag($newValue, $this);
	}

	public function scalarize($val) {
		if(is_null($val) || is_scalar($val)) {
			return $val;
		} else if(is_array($val)) {
			return array_map([$this, 'scalarize'], $val);
		} else {
			return (string)$val;
		}
	}

	public function remove($key) {
		return $this->replace($key, NULL);
	}

	public function __toString() {
		

		// echo "<pre>";
		// var_dump($arrays);

		return urldecode(http_build_query($this->toArray(), '_', '&'));
	}

	public function toArray() {
		$arrays = [];
		$bag = $this;
		do {
			$arrays[] = $bag->values;
			$bag = $bag->parent;
		} while($bag !== NULL);

		return array_replace_recursive(...array_reverse($arrays));
	}

	public function offsetGet($offset) {
		if($this->parent !== NULL) {
			return $this->parent[$offset];
		} else {
			return $this->values[$offset] ?? NULL;
		}
	}

	public function offsetSet($offset, $value) {
		
	}

	public function offsetExists($offset) {
		if($this->parent !== NULL) {
			return isset($this->parent[$offset]);
		} else {
			return isset($this->values[$offset]);
		}
	}

	public function offsetUnset($offset) {
		
	}

	public function only($key) {
		return new ParameterBag([$key => $this->toArray()[$key] ?? NULL]);
	}
}