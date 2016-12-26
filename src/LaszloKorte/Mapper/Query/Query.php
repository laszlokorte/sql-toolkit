<?php

namespace LaszloKorte\Mapper\Query;

use LaszloKorte\Mapper\Query\Condition\Top;
use LaszloKorte\Mapper\Query\Condition\Predicate;
use LaszloKorte\Mapper\Type;

final class Query {
	private $type;
	private $condition;
	private $limit = NULL;
	private $offset = 0;
	private $ordering = [];

	public function __construct(Type $type, Predicate $condition = NULL, $limit = NULL, $offset = 0, Ordering ...$orderings) {
		$this->type = $type;
		$this->condition = is_null($condition) ? new Top() : $condition;
		$this->limit = $limit;
		$this->offset = $offset;
		$this->ordering = is_null($ordering) ? [] : $ordering;
	}

	public function __clone() {
		
	}

	public function getType() {
		return $this->type;
	}

	public function getLimit() {
		return $this->limit;
	}

	public function getOffset() {
		return $this->offset;
	}

	public function getOrdering() {
		return $this->ordering;
	}

	public function getCondition() {
		return $this->condition;
	}

	public function withCondition(Predicate $condition) {
		return new self(
			$this->type,
			$condition,
			$this->limit,
			$this->offset,
			...$this->ordering
		);
	}

	public function withOrder(Ordering ...$orderings) {
		return new self(
			$this->type,
			$this->condition,
			$this->limit,
			$this->offset,
			...$orderings
		);
	}

	public function withLimit($limit) {
		return new self(
			$this->type,
			$this->condition,
			$limit,
			$this->offset,
			...$this->ordering
		);
	}

	public function withOffset($offset) {
		return new self(
			$this->type,
			$this->condition,
			$this->limit,
			$offset,
			...$this->ordering
		);
	}

	public function isStrictSubsetOf(Query $other) {
		return false;
	}
}
