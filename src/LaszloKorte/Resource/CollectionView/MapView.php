<?php

namespace LaszloKorte\Resource\CollectionView;

final class MapView {
	private $fields;
	private $collection;

	public function __construct($fields, $collection) {
		$this->fields = $fields;
		$this->collection = $collection;
	}

	public function getFields() {
		return $this->fields;
	}

	public function getQuery() {
		return $this->collection->getQuery();
	}

	public function getPagination() {
		return $this->collection->getPagination();
	}

	public function getTemplateName() {
		return 'table';
	}

	public function getRecords() {
		return $this->collection->getRecords();
	}
}