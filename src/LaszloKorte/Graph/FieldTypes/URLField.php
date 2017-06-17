<?php

namespace LaszloKorte\Graph\FieldTypes;

use LaszloKorte\Graph\FieldTypes\FieldType;
use LaszloKorte\Graph\Identifier;

use Serializable;

class URLField implements FieldType, Serializable {
	private $columnId;

	public function __construct(Identifier $columnId) {
		$this->columnId = $columnId;
	}
	public function getTemplateName() {
		return 'url';
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
			$this->columnId,
		]);
	}

	public function unserialize($data) {
		list(
			$this->columnId,
		) = unserialize($data);
	}
}