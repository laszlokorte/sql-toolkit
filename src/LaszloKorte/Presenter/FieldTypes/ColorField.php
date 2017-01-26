<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;

class ColorField implements FieldType {
	private $columnId;

	public function __construct($columnId) {
		$this->columnId = $columnId;
	}

	public function getTemplateName() {
		return 'color';
	}

	public function getRelatedColumns() {
		return [$this->columnId];
	}
}