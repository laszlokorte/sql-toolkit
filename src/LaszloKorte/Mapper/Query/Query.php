<?php

namespace LaszloKorte\Mapper\Query;

use LaszloKorte\Mapper\Query\Condition\Top;

class Query {
	private $condition;
	private $limit = NULL;
	private $offset = 0;
	private $ordering = [];

	public function __construct() {
		$this->condition = new Top();
	}

	public function __clone() {
		
	}
}
