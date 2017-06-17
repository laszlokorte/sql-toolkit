<?php

namespace LaszloKorte\Graph\FieldTypes;

use LaszloKorte\Graph\FieldTypes\FieldType;
use LaszloKorte\Graph\Identifier;

use Serializable;

class PasswordField implements FieldType, Serializable {
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

	public function serialize() {
		return serialize([
			$this->columnId,
			$this->requireRepeat,
		]);
	}

	public function unserialize($data) {
		list(
			$this->columnId,
			$this->requireRepeat,
		) = unserialize($data);
	}
}