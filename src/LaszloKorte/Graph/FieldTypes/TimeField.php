<?php

namespace LaszloKorte\Graph\FieldTypes;

use LaszloKorte\Graph\FieldTypes\FieldType;
use LaszloKorte\Graph\Identifier;

use Serializable;

class TimeField implements FieldType, Serializable {
	private $includesSeconds = false;
	private $columnId;

	public function __construct($includesSeconds, Identifier $columnId) {
		$this->includesSeconds = $includesSeconds;
		$this->columnId = $columnId;
	}

	public function getTemplateName() {
		return 'time';
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

	public function includesSeconds() {
		return $this->includesSeconds;
	}

	public function serialize() {
		return serialize([
			$this->includesSeconds,
			$this->columnId,
		]);
	}

	public function unserialize($data) {
		list(
			$this->includesSeconds,
			$this->columnId,
		) = unserialize($data);
	}
}