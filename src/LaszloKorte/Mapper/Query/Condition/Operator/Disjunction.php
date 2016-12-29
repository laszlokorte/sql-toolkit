<?php

namespace LaszloKorte\Mapper\Query\Condition\Operator;

use LaszloKorte\Mapper\Query\Condition\Predicate;
use LaszloKorte\Mapper\Query\Condition\OperatorTrait;
use LaszloKorte\Mapper\Record\Record;

final class Disjunction implements Predicate {
	use OperatorTrait;

	private $children;

	public function __construct(Predicate ...$children) {
		$this->children = $children;
	}

	public function evalFor(Record $record) {

	}

	public function getRootType() {
		return NULL;
	}

	public function getPaths() {
		return array_merge(
			...array_map(function($c) {
				return $c->getPaths();
			}, $this->children)
		);
	}
}
