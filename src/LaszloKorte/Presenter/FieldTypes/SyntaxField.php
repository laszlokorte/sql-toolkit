<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;

class SyntaxField implements FieldType {
	private $grammar;
	private $columnId;

	public function __construct($grammar, $columnId) {
		$this->grammar = $grammar;
		$this->columnId = $columnId;
	}

	public function getTemplateName() {
		return 'syntax';
	}

	public function getRelatedColumns() {
		return [$this->columnId];
	}
}