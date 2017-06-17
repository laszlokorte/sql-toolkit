<?php

namespace LaszloKorte\Graph\FieldTypes;

use LaszloKorte\Graph\FieldTypes\FieldType;

use Serializable;

class CompositeField implements FieldType, Serializable {
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

	public function serialize() {
		return serialize([
			$this->childFields,
		]);
	}

	public function unserialize($data) {
		list(
			$this->childFields,
		) = unserialize($data);
	}
}