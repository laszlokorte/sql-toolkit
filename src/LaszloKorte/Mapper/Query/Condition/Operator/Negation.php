<?php

namespace LaszloKorte\Mapper\Query\Condition\Operator;

use LaszloKorte\Mapper\Query\Condition\Predicate;
use LaszloKorte\Mapper\Query\Condition\OperatorTrait;
use LaszloKorte\Mapper\Record\Record;

final class Negation implements Predicate {
	use OperatorTrait;

	private $operand;

	public function __construct(Predicate $operand) {
		$this->operand = $operand;
	}

	public function evalFor(Record $record) {

	}

	public function getRootType() {
		return NULL;
	}

	public function _not() {
		return $this->operand;
	}

	public function getPaths() {
		return $this->operand->getPaths();
	}
}
