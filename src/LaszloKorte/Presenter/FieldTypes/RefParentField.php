<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;
use LaszloKorte\Presenter\Association\AssociationDefinition;

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
		return $this->fkOwnColumnNames;
	}

	public function getChildAssociations() {
		return [];
	}

	public function getParentAssociations() {
		return [
			new AssociationDefinition($this->entityId, $this->fkOwnColumnNames),
		];
	}
}