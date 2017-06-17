<?php

namespace LaszloKorte\Graph\FieldTypes;

use LaszloKorte\Graph\FieldTypes\FieldType;
use LaszloKorte\Graph\Identifier;

use Serializable;

class SyntaxField implements FieldType, Serializable {
	private $grammar;
	private $columnId;

	public function __construct($grammar, Identifier $columnId) {
		$this->grammar = $grammar;
		$this->columnId = $columnId;
	}

	public function getTemplateName() {
		return 'syntax';
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
			$this->grammar,
		]);
	}

	public function unserialize($data) {
		list(
			$this->columnId,
			$this->grammar,
		) = unserialize($data);
	}
}