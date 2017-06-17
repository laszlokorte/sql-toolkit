<?php

namespace LaszloKorte\Graph\FieldTypes;

use LaszloKorte\Graph\FieldTypes\FieldType;
use LaszloKorte\Graph\Identifier;

use Serializable;

class CurrencyField implements FieldType, Serializable {
	private $columnId;
	private $unit;

	public function __construct($unit, Identifier $columnId) {
		$this->unit = $unit;
		$this->columnId = $columnId;
	}

	public function getUnit() {
		return $this->unit;
	}

	public function getTemplateName() {
		return 'currency';
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
			$this->unit,
			$this->columnId,
		]);
	}

	public function unserialize($data) {
		list(
			$this->unit,
			$this->columnId,
		) = unserialize($data);
	}
}