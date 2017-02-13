<?php

namespace LaszloKorte\Graph\FieldTypes;

use LaszloKorte\Graph\FieldTypes\FieldType;
use LaszloKorte\Graph\Identifier;

class DateTimeField implements FieldType {
	private $columnId;

	public function __construct(Identifier $columnId) {
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