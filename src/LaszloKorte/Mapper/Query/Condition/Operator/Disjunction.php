<?php

namespace LaszloKorte\Mapper\Query\Condition\Operator;

use LaszloKorte\Mapper\Query\Condition\Predicate;
use LaszloKorte\Mapper\Query\Condition\OperatorTrait;
use LaszloKorte\Mapper\Record\Record;

class Disjunction implements Predicate {
	use OperatorTrait;

	public function evalFor(Record $record) {

	}

	public function getRootType() {
		return NULL;
	}
}
