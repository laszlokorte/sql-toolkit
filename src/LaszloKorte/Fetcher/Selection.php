<?php

namespace LaszloKorte\Fetcher;

use LaszloKorte\Schema\Identifier;
use LaszloKorte\Fetcher\Predicate;

class Selection {
	private $tableName;
	private $projections = [];
	private $joins = [];
	private $condition = new Predicate\Top();
	private $limit;
	private $offset;
	private $groupBy = [];
	private $having = new Predicate\Top();

	public function __construct(Identifier $tableName) {
		$this->tableName = $tableName;
	}
}
