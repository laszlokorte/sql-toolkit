<?php

namespace LaszloKorte\Graph\Template\Nodes;

use LaszloKorte\Graph\Entity;
use LaszloKorte\Resource\Query\Record;

use Serializable;

final class OutputTag implements Serializable {

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

	public function serialize() {
		return serialize([
			$this->path,
			$this->filters,
		]);
	}

	public function unserialize($data) {
		list(
			$this->path,
			$this->filters,
		) = unserialize($data);
	}
}