<?php

namespace LaszloKorte\Mapper\Collection;

use LaszloKorte\Mapper\Type;
use LaszloKorte\Mapper\Record\Record;
use LaszloKorte\Mapper\Record\Identifier;
use LaszloKorte\Mapper\Query\Query;
use LaszloKorte\Mapper\Query\Condition;
use LaszloKorte\Mapper\Query\Ordering;

use IteratorAggregate;
use Countable;

class Collection implements IteratorAggregate, Countable {
	private $query;
	private $type;
	private $rows;
	private $cache;

	public function __construct(Query $query, Type $type, array $rows = NULL, SiblingCache $cache) {
		$this->query = $query;
		$this->type = $type;
		$this->rows = $rows;
		$this->cache = $cache;
	}

	public function getType() {
		return $this->type;
	}

	private function deriveFromQuery(Query $query) {
		return new Collection($query, $this->type);
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

	public function limit($count) {
		return $this->deriveFromQuery(clone $this->query);
	}

	public function offset($count) {
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
