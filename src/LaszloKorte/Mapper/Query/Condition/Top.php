<?php

namespace LaszloKorte\Mapper\Query\Condition;

use LaszloKorte\Mapper\Record\Record;

final class Top implements Predicate {
	use OperatorTrait;

	public function evalFor(Record $record) {
		return TRUE;
	}

	public function getRootType() {
		return NULL;
	}

	public function _not() {
		return new Bottom();
	}

	public function getPaths() {
		return [];
	}
}
