<?php

namespace LaszloKorte\Mapper\Collection;

use LaszloKorte\Mapper\Type;
use LaszloKorte\Mapper\Record\Record;
use LaszloKorte\Mapper\Record\Identifier;
use LaszloKorte\Mapper\Query\Query;
use LaszloKorte\Mapper\Query\Condition;
use LaszloKorte\Mapper\Query\Ordering;

class LazyCollection implements Collection {
	private $query;

	public function __construct(Query $query) {
		$this->query = $query;
	}

	public function getType() {
		return $this->query->getType();
	}

	private function deriveFromQuery(Query $query) {
		return new Collection($query);
	}

	public function filter(Condition $cond) {
		return $this->deriveFromQuery(clone $this->query);
	}

	public function expand(Condition $cond) {
		return $this->deriveFromQuery(clone $this->query);
	}

	public function orderBy(Ordering $cond) {
		return $this->deriveFromQuery(clone $this->query);
	}

	public function take($limit) {
		return $this->deriveFromQuery(clone $this->query);
	}

	public function skip($offset) {
		return $this->deriveFromQuery(clone $this->query);
	}

	public function getRecords() {
		$this->loadResult();
		return array_map(function($r) {
			return $this->recordFromRow($r);
		}, $this->rows);
	}

	public function getIterator() {
		return new Iterator($this);
	}

	public function count() {
		$this->loadResult();

		return count($this->rows);
	}

	public function get($idx) {
		$this->loadResult();

		return $this->recordFromRow($this->rows[$idx]);
	}

	private function recordFromRow($row) {
		return new Record($this->keyFromValues($values), $obj, $this);
	}

	private function keyFromValues($values) {
		return $this->getType()->keyFromValues($values);
	}

	private function loadResult($reload = FALSE) {
		if(!$reload && $this->resultSet !== NULL) {
			return;
		}
		$this->rows = $this->getType()->query($this->query);
	}
}
