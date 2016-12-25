<?php

namespace LaszloKorte\Mapper\Query;

use LaszloKorte\Mapper\Query\Condition\Top;
use LaszloKorte\Mapper\Type;

class Query {
	private $type;
	private $condition;
	private $limit = NULL;
	private $offset = 0;
	private $ordering = [];

	public function __construct(Type $type) {
		$this->type = $type;
		$this->condition = new Top();
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
}
