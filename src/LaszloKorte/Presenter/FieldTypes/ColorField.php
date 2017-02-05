<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;
use LaszloKorte\Presenter\Identifier;

class ColorField implements FieldType {
	private $columnId;

	public function __construct(Identifier $columnId) {
		$this->columnId = $columnId;
	}

	public function getTemplateName() {
		return 'color';
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