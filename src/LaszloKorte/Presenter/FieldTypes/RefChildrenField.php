<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;

class RefChildrenField implements FieldType {
	private $entityId;
	private $fkOtherColumnNames;

	public function __construct($entityId, $fkOtherColumnNames) {
		$this->entityId = $entityId;
		$this->fkOtherColumnNames = $fkOtherColumnNames;
	}

	public function getTemplateName() {
		return 'ref-children';
	}

	public function getRelatedColumns() {
		return [];
	}
}