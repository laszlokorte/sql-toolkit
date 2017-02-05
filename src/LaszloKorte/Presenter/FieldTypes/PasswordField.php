<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;
use LaszloKorte\Presenter\Identifier;

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
		return [$this->columnId];
	}

	public function getChildAssociations() {
		return [];
	}

	public function getParentAssociations() {
		return [];
	}
}