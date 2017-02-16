<?php

namespace LaszloKorte\Graph\FieldTypes;

use LaszloKorte\Graph\FieldTypes\FieldType;
use LaszloKorte\Graph\Identifier;

class DateField implements FieldType {
	private $columnId;

	public function __construct(Identifier $columnId) {
		$this->columnId = $columnId;
	}

	public function getTemplateName() {
		return 'date';
	}

	public function getRelatedColumns() {
		return ['value' => $this->columnId];
	}

	public function getChildAssociations() {
		return [];
	}

	public function getParentAssociations() {
		return [];
	}

}