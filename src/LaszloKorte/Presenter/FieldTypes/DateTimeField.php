<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;

class DateTimeField implements FieldType {
	private $columnId;

	public function __construct($columnId) {
		$this->columnId = $columnId;
	}

	public function getTemplateName() {
		return 'datetime';
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