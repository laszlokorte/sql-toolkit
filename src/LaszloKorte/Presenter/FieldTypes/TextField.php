<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;

class TextField implements FieldType {
	const TYPE_SINGLE_LINE = 1;
	const TYPE_MULTI_LINE = 2;

	private $type;
	private $columnId;

	public function __construct($type, $columnId) {
		$this->type = $type;
		$this->columnId = $columnId;
	}

	public function getTemplateName() {
		return 'text';
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