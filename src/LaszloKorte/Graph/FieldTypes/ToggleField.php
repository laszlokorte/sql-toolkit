<?php

namespace LaszloKorte\Graph\FieldTypes;

use LaszloKorte\Graph\FieldTypes\FieldType;
use LaszloKorte\Graph\Identifier;

use Serializable;

class ToggleField implements FieldType, Serializable {
	const TYPE_RADIO = 1;
	const TYPE_CHECKBOX = 2;

	private $type;
	private $columnId;

	public function __construct($type, Identifier $columnId) {
		$this->type = $type;
		$this->columnId = $columnId;
	}

	public function getTemplateName() {
		return 'toggle';
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
			$this->type,
			$this->columnId,
		]);
	}

	public function unserialize($data) {
		list(
			$this->type,
			$this->columnId,
		) = unserialize($data);
	}
}