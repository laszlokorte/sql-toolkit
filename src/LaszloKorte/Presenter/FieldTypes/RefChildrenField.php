<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;
use LaszloKorte\Presenter\Association\AssociationDefinition;

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

	public function getChildAssociations() {
		return [
			new AssociationDefinition($this->entityId, $this->fkOtherColumnNames),
		];
	}

	public function getParentAssociations() {
		return [];
	}
}