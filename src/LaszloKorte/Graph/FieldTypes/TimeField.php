<?php

namespace LaszloKorte\Graph\FieldTypes;

use LaszloKorte\Graph\FieldTypes\FieldType;
use LaszloKorte\Graph\Identifier;

class TimeField implements FieldType {
	private $includeSeconds = false;
	private $columnId;

	public function __construct($includeSeconds, Identifier $columnId) {
		$this->includeSeconds = $includeSeconds;
		$this->columnId = $columnId;
	}

	public function getTemplateName() {
		return 'time';
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