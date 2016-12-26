<?php

namespace LaszloKorte\Mapper\Query\Condition;

use LaszloKorte\Mapper\Record\Record;
use LaszloKorte\Mapper\Query\Condition\Value\ConstantValue;

final class Like implements Predicate {
	use OperatorTrait;

	private $valueA;
	private $valueB;

	public function __construct(Value $valueA, ConstantValue $valueB) {
		$this->valueA = $valueA;
		$this->valueB = $valueB;
	}

	public function evalFor(Record $record) {

	}

	public function getRootType() {
		return NULL;
	}
}
