<?php

namespace LaszloKorte\Graph\FieldTypes;

use LaszloKorte\Graph\FieldTypes\FieldType;
use LaszloKorte\Graph\Association\AssociationDefinition;
use LaszloKorte\Graph\Identifier;


class RefParentField implements FieldType {
	private $entityId;
	private $fkOwnColumnNames;
	private $fkChildColumnNames;

	public function __construct(Identifier $entityId, array $fkOwnColumnNames, array $fkChildColumnNames) {
		$this->entityId = $entityId;
		$this->fkOwnColumnNames = $fkOwnColumnNames;
		$this->fkChildColumnNames = $fkChildColumnNames;
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
			new AssociationDefinition($this->entityId, $this->fkOwnColumnNames, $this->fkChildColumnNames),
		];
	}
}