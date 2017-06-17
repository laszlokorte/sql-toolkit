<?php

namespace LaszloKorte\Graph\FieldTypes;

use LaszloKorte\Graph\FieldTypes\FieldType;
use LaszloKorte\Graph\Identifier;

use Serializable;

class RefManyField implements FieldType, Serializable {
	const STYLE_TAGS = 1;
	const STYLE_DETAILED = 2;

	private $style;
	private $joinEntityId;
	private $fkOwnColumnNames;
	private $fkOtherColumnNames;

	public function __construct($style, Identifier $joinEntityId, array $fkOwnColumnNames, array $fkOtherColumnNames) {
		$this->style = $style;
		$this->joinEntityId = $joinEntityId;
		$this->fkOwnColumnNames = $fkOwnColumnNames;
		$this->fkOtherColumnNames = $fkOtherColumnNames;
	}

	public function getTemplateName() {
		return 'ref_many';
	}

	public function getRelatedColumns() {
		return [];
	}

	public function getChildAssociations() {
		return [];
	}

	public function getParentAssociations() {
		return [];
	}

	public function serialize() {
		return serialize([
			$this->style,
			$this->joinEntityId,
			$this->fkOwnColumnNames,
			$this->fkOtherColumnNames,
		]);
	}

	public function unserialize($data) {
		list(
			$this->style,
			$this->joinEntityId,
			$this->fkOwnColumnNames,
			$this->fkOtherColumnNames,
		) = unserialize($data);
	}
}