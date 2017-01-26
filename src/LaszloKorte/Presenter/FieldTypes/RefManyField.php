<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;

class RefManyField implements FieldType {
	const STYLE_TAGS = 1;
	const STYLE_DETAILED = 2;

	private $style;
	private $joinEntityId;
	private $fkOwnColumns;
	private $fkOtherColumnNames;

	public function __construct($style, $joinEntityId, $fkOwnColumnNames, $fkOtherColumnNames) {
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
}