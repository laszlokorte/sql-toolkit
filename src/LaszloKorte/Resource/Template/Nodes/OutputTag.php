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

	public function getPath() {
		return $this->path;
	}

	public function getFilters() {
		return $this->filters;
	}

	public function hasFilters() {
		return !empty($this->filters);
	}

	public function __toString() {
		return sprintf(
			'{{ %s }}', 
			implode(' | ', array_merge([$this->path], $this->filters))
		);
	}
}