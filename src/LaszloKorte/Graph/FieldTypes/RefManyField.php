<?php

namespace LaszloKorte\Graph\FieldTypes;

use LaszloKorte\Graph\FieldTypes\FieldType;
use LaszloKorte\Graph\Identifier;

class RefManyField implements FieldType {
	const STYLE_TAGS = 1;
	const STYLE_DETAILED = 2;

	private $style;
	private $joinEntityId;
	private $fkOwnColumns;
	private $fkOtherColumnNames;

	public function __construct($style, Identifier $joinEntityId, array $fkOwnColumnNames, array $fkOtherColumnNames) {
		$this->style = $style;
		$this->joinEntityId = $joinEntityId;
		$this->fkOwnColumnNames = $fkOwnColumnNames;
		$this->fkOtherColumnNames = $fkOtherColumnNames;
	}

	public function getTemplateName() {
		return 'ref-many';
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
}