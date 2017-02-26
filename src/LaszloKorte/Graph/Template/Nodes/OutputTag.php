<?php

namespace LaszloKorte\Graph\Template\Nodes;

use LaszloKorte\Graph\Entity;
use LaszloKorte\Resource\Query\Record;

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

	public function render($link, $record) {
		return array_reduce($this->filters, function($acc, $filter) {
			return $acc;
		}, $this->path->render($link, $record));
	}
}