<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;
use LaszloKorte\Presenter\Identifier;

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