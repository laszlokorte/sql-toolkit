<?php

namespace LaszloKorte\Graph\FieldTypes;

use LaszloKorte\Graph\FieldTypes\FieldType;
use LaszloKorte\Graph\Identifier;

class ChoiceField implements FieldType {
	private $multiple;
	private $choices = [];
	private $columnId;

	public function __construct($multiple, $choices, Identifier $columnId) {
		$this->multiple = $multiple;
		$this->choices = $choices;
		$this->columnId = $columnId;
	}

	public function getTemplateName() {
		return 'choice';
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