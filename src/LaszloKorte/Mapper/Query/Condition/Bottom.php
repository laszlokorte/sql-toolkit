<?php

namespace LaszloKorte\Mapper\Query\Condition;

use LaszloKorte\Mapper\Record\Record;

final class Bottom implements Predicate {
	use OperatorTrait;

	public function evalFor(Record $record) {
		return FALSE;
	}

	public function getRootType() {
		return NULL;
	}

	public function _not() {
		return new Top();
	}

	public function getPaths() {
		return [];
	}
}
