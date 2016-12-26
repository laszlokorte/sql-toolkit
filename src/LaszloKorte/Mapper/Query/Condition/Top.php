<?php

namespace LaszloKorte\Mapper\Query\Condition;

use LaszloKorte\Mapper\Record\Record;

class Top implements Predicate {
	use OperatorTrait;

	public function evalFor(Record $record) {
		return true;
	}

	public function getRootType() {
		return NULL;
	}
}
