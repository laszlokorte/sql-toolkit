<?php

namespace LaszloKorte\Graph\FieldTypes;

use LaszloKorte\Graph\FieldTypes\FieldType;
use LaszloKorte\Graph\Association\AssociationDefinition;
use LaszloKorte\Graph\Identifier;


class RefParentField implements FieldType {
	private $entityId;
	private $fkOwnColumnNames;

	public function __construct(Identifier $entityId, array $fkOwnColumnNames) {
		$this->entityId = $entityId;
		$this->fkOwnColumnNames = $fkOwnColumnNames;
	}

	public function getTemplateName() {
		return 'ref_parent';
	}

	public function getRelatedColumns() {
		return $this->fkOwnColumnNames;
	}

	public function getChildAssociations() {
		return [];
	}

	public function getParentAssociations() {
		return [
			'ref' => 
			new AssociationDefinition($this->entityId, $this->fkOwnColumnNames),
		];
	}
}