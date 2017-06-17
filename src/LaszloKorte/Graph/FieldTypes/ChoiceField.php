<?php

namespace LaszloKorte\Graph\FieldTypes;

use LaszloKorte\Graph\FieldTypes\FieldType;
use LaszloKorte\Graph\Identifier;

use Serializable;

class ChoiceField implements FieldType, Serializable {
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
		return ['value' => $this->columnId];
	}

	public function getChildAssociations() {
		return [];
	}

	public function getParentAssociations() {
		return [];
	}

	public function serialize() {
		return serialize([
			$this->multiple,
			$this->choices,
			$this->columnId,
		]);
	}

	public function unserialize($data) {
		list(
			$this->multiple,
			$this->choices,
			$this->columnId,
		) = unserialize($data);
	}
}