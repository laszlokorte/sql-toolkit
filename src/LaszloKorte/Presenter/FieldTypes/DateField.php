<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;

class DateField implements FieldType {
	private $columnId;

	public function __construct($columnId) {
		$this->columnId = $columnId;
	}

	public function getTemplateName() {
		return 'date';
	}

	public function getRelatedColumns() {
		return [$this->columnId];
	}

	public function getChildAssociations() {
		return [];
	}

	public function getParentAssociations() {
		return [];
	}

}