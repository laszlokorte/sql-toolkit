<?php

namespace LaszloKorte\Query;

use ArrayAccess;

final class ParameterBag implements ArrayAccess {

	private $values;
	private $parent;
	
	public function __construct($values, $parent = NULL) {
		$this->values = $values;
		$this->parent = $parent;
	}

	public function replace($key, $value) {
		$reversePath = array_reverse(
			is_array($key) ? $key : explode('.', $key)
		);
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
			return $this->values[$offset];
		}
	}

	public function offsetSet($offset, $value) {
		
	}

	public function offsetExists($offset) {
		
	}

	public function offsetUnset($offset) {
		
	}
}