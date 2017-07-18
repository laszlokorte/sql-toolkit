<?php

namespace LaszloKorte\Resource\CollectionView;

final class TableViewColumn {

	private $field;

	public function __construct($field, $collection) {
		$this->field = $field;
		$this->collection = $collection;
	}

	public function toggleOrderParams($params) {
		if($this->isOrderBy('asc')) {
			return $this->orderParams($params, 'desc');
		} else {
			return $this->orderParams($params, 'asc');
		}
	}

	public function orderParams($params, $direction) {
		return $this->collection->getOrdering()->buildParams(
			$this->collection->getPagination()->resetParams($params), 
			$this->field->id(), $direction
		);
	}

	public function isOrderBy($direction = NULL) {
		return $this->collection->getOrdering()->isOrderedBy($this->field->id(), $direction);
	}

	public function getTitle() {
		return $this->field->title();
	}

	public function isLinked() {
		return $this->field->isLinked();
	}

	public function getField() {
		return $this->field;
	}

}