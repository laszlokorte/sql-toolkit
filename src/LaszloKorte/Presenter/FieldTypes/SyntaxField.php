<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;
use LaszloKorte\Presenter\Identifier;

class SyntaxField implements FieldType {
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
		return [$this->columnId];
	}

	public function getChildAssociations() {
		return [];
	}

	public function getParentAssociations() {
		return [];
	}
}