<?php

namespace LaszloKorte\Graph\FieldTypes;

use LaszloKorte\Graph\FieldTypes\FieldType;
use LaszloKorte\Graph\Identifier;

class PasswordField implements FieldType {
	private $requireRepeat = false;
	private $columnId;

	public function __construct($requireRepeat, Identifier $columnId) {
		$this->requireRepeat = $requireRepeat;
		$this->columnId = $columnId;
	}

	public function getTemplateName() {
		return 'password';
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
}