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
		return 'ref_children';
	}

	public function getRelatedColumns() {
		return [];
	}

	public function getChildAssociations() {
		return [
			'ref' => 
			new AssociationDefinition($this->entityId, $this->fkOtherColumnNames),
		];
	}

	public function getParentAssociations() {
		return [];
	}
}