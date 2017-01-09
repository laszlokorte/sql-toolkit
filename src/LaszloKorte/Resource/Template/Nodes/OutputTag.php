<?php

namespace LaszloKorte\Resource\Template\Nodes;

final class OutputTag {

	private $path;
	private $filters;

	public function __construct(Path $path) {
		$this->path = $path;
		$this->filters = [];
	}

	public function addFilter(Filter $filter) {
		$this->filters []= $filter; 
	}
}