<?php

namespace LaszloKorte\Query;

use ArrayAccess;

final class ParameterBag implements ArrayAccess {

	private $values;
	private $parent;
	
	public function __construct(array $values, $parent = NULL) {
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
		$arrays = [];
		$bag = $this;
		do {
			$arrays[] = $bag->values;
			$bag = $bag->parent;
		} while($bag !== NULL);

		// echo "<pre>";
		// var_dump($arrays);

		return urldecode(http_build_query(array_replace_recursive(...array_reverse($arrays)), '_', '&'));
	}

	public function offsetGet($offset) {
		if($this->parent !== NULL) {
			return $this->parent[$offset];
		} else {
			return $this->values[$offset] ?? Null;
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
}