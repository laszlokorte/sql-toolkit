<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;
use LaszloKorte\Presenter\Association\AssociationDefinition;
use LaszloKorte\Presenter\Identifier;


class RefParentField implements FieldType {
	private $entityId;
	private $fkOwnColumnNames;

	public function __construct(Identifier $entityId, array $fkOwnColumnNames) {
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