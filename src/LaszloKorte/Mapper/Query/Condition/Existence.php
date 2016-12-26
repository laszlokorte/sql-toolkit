<?php

namespace LaszloKorte\Mapper\Query\Condition;

use LaszloKorte\Mapper\Record\Record;
use LaszloKorte\Mapper\Query\Condition\Value\Value;

final class Existence implements Predicate {
	use OperatorTrait;

	private $value;
	private $query;

	public function __construct(Value $value, Query $query) {
		$this->value = $value;
		$this->query = $query;
	}

	public function evalFor(Record $record) {

	}

	public function getRootType() {
		return NULL;
	}
}
