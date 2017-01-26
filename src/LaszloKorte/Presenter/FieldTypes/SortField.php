<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;

class SortField implements FieldType {
	
	private $columnId;

	public function __construct($columnId) {
		$this->columnId = $columnId;
	}

	public function getTemplateName() {
		return 'sort';
	}

	public function getRelatedColumns() {
		return [$this->columnId];
	}
}