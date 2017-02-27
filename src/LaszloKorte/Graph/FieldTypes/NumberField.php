<?php

namespace LaszloKorte\Graph\FieldTypes;

use LaszloKorte\Graph\FieldTypes\FieldType;
use LaszloKorte\Graph\Identifier;

class NumberField implements FieldType {
	private $columnId;
	private $unit;

	public function __construct(Identifier $columnId, $unit = null) {
		$this->columnId = $columnId;
		$this->unit = $unit;
	}

	public function getUnit() {
		return $this->unit;
	}

	public function getTemplateName() {
		return 'number';
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