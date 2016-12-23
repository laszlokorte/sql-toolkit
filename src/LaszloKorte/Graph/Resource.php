<?php

namespace LaszloKorte\Graph;

class Resource {
	private $graph;
	private $name;

	public function __construct(ResourceGraph $graph, $name) {
		$this->name = $name;
		$this->graph = $graph;
	}
}