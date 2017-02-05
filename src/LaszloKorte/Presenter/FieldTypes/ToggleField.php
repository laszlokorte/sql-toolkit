<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;
use LaszloKorte\Presenter\Identifier;

class ToggleField implements FieldType {
	const TYPE_RADIO = 1;
	const TYPE_CHECKBOX = 2;

	private $type;
	private $columnId;

	public function __construct($type, Identifier $columnId) {
		$this->type = $type;
		$this->columnId = $columnId;
	}

	public function getTemplateName() {
		return 'toggle';
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