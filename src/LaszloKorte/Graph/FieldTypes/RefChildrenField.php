<?php

namespace LaszloKorte\Graph\FieldTypes;

use LaszloKorte\Graph\FieldTypes\FieldType;
use LaszloKorte\Graph\Association\AssociationDefinition;
use LaszloKorte\Graph\Identifier;

class RefChildrenField implements FieldType {
	private $entityId;
	private $fkOtherColumnNames;

	public function __construct(Identifier $entityId, $fkOtherColumnNames) {
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