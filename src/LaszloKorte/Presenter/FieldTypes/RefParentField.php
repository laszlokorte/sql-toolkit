<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;

class RefParentField implements FieldType {
	private $entityId;
	private $fkOwnColumnNames;

	public function __construct($entityId, $fkOwnColumnNames) {
		$this->entityId = $entityId;
		$this->fkOwnColumnNames = $fkOwnColumnNames;
	}

	public function getTemplateName() {
		return 'ref-parent';
	}

	public function getRelatedColumns() {
		return [];
	}
}