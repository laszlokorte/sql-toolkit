<?php

namespace LaszloKorte\Resource\Collection;

final class Collection {
	private $records;
	private $pagination;
	private $ordering;
	private $query;

	function __construct($records, $pagination, $ordering, $query) {
		$this->records = $records;
		$this->pagination = $pagination;
		$this->ordering = $ordering;
		$this->query = $query;
	}

	public function getPagination() {
		return $this->pagination;
	}

	public function getOrdering() {
		return $this->ordering;
	}

	public function getRecords() {
		return $this->records;
	}

	public function getQuery() {
		return $this->query;
	}
}