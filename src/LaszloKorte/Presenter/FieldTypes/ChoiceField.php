<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;

class ChoiceField implements FieldType {
	private $multiple;
	private $choices = [];
	private $columnId;

	public function __construct($multiple, $choices, $columnId) {
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
}