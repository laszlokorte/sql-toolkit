<?php

namespace LaszloKorte\Resource\CollectionView;

final class TableView {
	private $fields;
	private $collection;

	public function __construct($fields, $collection) {
		$this->fields = $fields;
		$this->collection = $collection;
	}

	public function getFields() {
		return $this->fields;
	}

	public function getColumns() {
		return array_map(function($field) {
			return new TableViewColumn($field, $this->collection);
		}, $this->fields);
	}

	public function getQuery() {
		return $this->collection->getQuery();
	}

	public function getPagination() {
		return $this->collection->getPagination();
	}

	public function getOrdering() {
		return $this->collection->getOrdering();
	}

	public function getTemplateName() {
		return 'table';
	}

	public function getRecords() {
		return $this->collection->getRecords();
	}

	public function toggleSerialOrderParams($params) {
		if($this->isSerialOrderBy('asc')) {
			return $this->serialOrderParams($params, 'desc');
		} else {
			return $this->serialOrderParams($params, 'asc');
		}
	}

	public function serialOrderParams($params, $direction) {
		return $this->collection->getOrdering()->buildParams(
			$this->collection->getPagination()->resetParams($params), 
			false, $direction
		);
	}

	public function isSerialOrderBy($direction = NULL) {
		return $this->collection->getOrdering()->isOrderedBy(null, $direction);
	}
}