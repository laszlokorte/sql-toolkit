<?php

namespace LaszloKorte\Mapper\Query\Condition\Operator;

use LaszloKorte\Mapper\Query\Condition\Predicate;
use LaszloKorte\Mapper\Query\Condition\OperatorTrait;
use LaszloKorte\Mapper\Record\Record;

final class Conjunction implements Predicate {
	use OperatorTrait;

	private $lhs;
	private $rhs;

	public function __construct(Predicate $lhs, Predicate $rhs) {
		$this->lhs = $lhs;
		$this->rhs = $rhs;
	}

	public function evalFor(Record $record) {
		
	}

	public function getRootType() {
		return NULL;
	}

	public function getPaths() {
		return array_merge(
			$this->lhs->getPaths(), 
			$this->rhs->getPaths()
		);
	}
}
