<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;
use LaszloKorte\Presenter\Identifier;

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