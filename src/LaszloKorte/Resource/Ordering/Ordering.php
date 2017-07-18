<?php

namespace LaszloKorte\Resource\Ordering;

final class Ordering {
	private $fieldId;
	private $direction;

	public function __construct($fieldId, $direction) {
		$this->fieldId = $fieldId;
		$this->direction = $direction;
	}

	public function isOrderedBy($fieldId, $direction = NULL) {
		return ($this->fieldId == $fieldId) && 
			($direction === NULL || $this->direction === $direction);
	}

	public function modifyQuery($query) {
		
	}

	public function modifyQueryBuilder($queryBuilder) {
		if($this->fieldId !== NULL) {
			$queryBuilder->sortByField((string)$this->fieldId, $this->direction === 'asc');
		} else {
			$queryBuilder->sortDefault($this->direction === 'asc');
		}
	}

	public function transformResult($result) {
		return $result;
	}

	public function buildParams($params, $fieldId = NULL, $direction = NULL) {
		$dir = $direction ?: $this->direction ?: 'asc';
		$field = $fieldId !== NULL ? $fieldId : $this->fieldId;
		return $params
			->replace(['order', 'field'], $field ?: NULL)
			->replace(['order', 'dir'], $dir === 'asc' && $field === NULL ? NULL : $dir);
	}

	public function resetParams($params) {
		return $params->remove('order');
	}
}