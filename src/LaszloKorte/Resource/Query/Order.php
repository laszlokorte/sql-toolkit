<?php

namespace LaszloKorte\Resource\Query;

use LaszloKorte\Graph\Identifier;

use LaszloKorte\Graph\Path\Path;

final class Order {
	private $columnOrAggregation;
	private $direction;

	public function __construct($columnOrAggregation, $direction = 'ASC') {
		$this->columnOrAggregation = $columnOrAggregation;
		$this->direction = $direction;
	}

	public function getColumnOrAggregation() {
		return $this->columnOrAggregation;
	}

	public function getDirection() {
		return $this->direction;
	}
}