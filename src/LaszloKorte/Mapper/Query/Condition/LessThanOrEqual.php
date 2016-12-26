<?php

namespace LaszloKorte\Mapper\Query\Condition;

use LaszloKorte\Mapper\Record\Record;
use LaszloKorte\Mapper\Query\Condition\Value\Value;

final class LessThanOrEqual implements Predicate {
	use OperatorTrait;

	private $valueA;
	private $valueB;

	public function __construct(Value $valueA, Value $valueB) {
		$this->valueA = $valueA;
		$this->valueB = $valueB;
	}

	public function evalFor(Record $record) {

	}

	public function getRootType() {
		return NULL;
	}

	public function _not() {
		return new GreaterThan($this->valueA, $this->valueB);
	}
}
