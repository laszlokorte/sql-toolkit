<?php

namespace LaszloKorte\Mapper\Path;

use LaszloKorte\Mapper\Query\Condition;
use LaszloKorte\Mapper\Query\Ordering;
use LaszloKorte\Mapper\Query\Condition\Value\RecordValue;
use LaszloKorte\Mapper\Query\Condition\Value\ConstantValue;

trait FieldDSLTrait {
	public function count() {

	}

	public function avg() {

	}

	public function min() {

	}

	public function max() {

	}
	
	public function sum() {

	}

	public function eq($other) {
		return new Condition\Equal(new RecordValue($this), $this->valueFor($otherValue));
	}

	public function neq($other) {

	}

	public function lt($other) {

	}

	public function gt($other) {

	}
	
	public function lte($other) {

	}

	public function gte($other) {

	}

	public function like($other) {

	}

	public function in($list) {

	}

	public function asc() {
		return new Ordering($this, Ordering::ASC);
	}

	public function desc() {
		return new Ordering($this, Ordering::DESC);
	}

	private function valueFor($other) {
		if ($other instanceof FieldPath) {
			return new RecordValue($other);
		} else {
			return new ConstantValue($other);
		}
	}
}