<?php

namespace LaszloKorte\Graph;

use LaszloKorte\Schema\Schema;

class Graph {
	private $graphDefinition;

	public function __construct(GraphDefinition $graphDefinition) {
		$this->graphDefinition = $graphDefinition;
	}
}