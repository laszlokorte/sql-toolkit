<?php

namespace LaszloKorte\Graph\FieldTypes;

use LaszloKorte\Graph\FieldTypes\FieldType;
use LaszloKorte\Graph\Identifier;

use Serializable;

class TextField implements FieldType, Serializable {
	const TYPE_SINGLE_LINE = 1;
	const TYPE_MULTI_LINE = 2;

	private $type;
	private $columnId;

	public function __construct($type, Identifier $columnId) {
		$this->type = $type;
		$this->columnId = $columnId;
	}

	public function isMultiline() {
		return $this->type === self::TYPE_MULTI_LINE;
	}

	public function getTemplateName() {
		return 'text';
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