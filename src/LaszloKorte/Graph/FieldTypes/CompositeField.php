<?php

namespace LaszloKorte\Graph\FieldTypes;

use LaszloKorte\Graph\FieldTypes\FieldType;

class CompositeField implements FieldType {
	private $childFields;

	public function __construct($childFields) {
		$this->childFields = $childFields;
	}

	public function getTemplateName() {
		return 'composite';
	}

	public function getRelatedColumns() {
		return array_merge(...array_map(function($field) {
			return $field->getRelatedColumns();
		}, $this->childFields));
	}

	public function getChildAssociations() {
		return [];
	}

	public function getParentAssociations() {
		return [];
	}
}