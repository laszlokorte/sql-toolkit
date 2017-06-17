<?php

namespace LaszloKorte\Graph\FieldTypes;

use LaszloKorte\Graph\FieldTypes\FieldType;
use LaszloKorte\Graph\Association\AssociationDefinition;
use LaszloKorte\Graph\Identifier;

use Serializable;

class RefChildrenField implements FieldType, Serializable {
	private $entityId;
	private $fkParentColumnNames;
	private $fkOwnColumnNames;

	public function __construct(Identifier $entityId, $fkParentColumnNames, array $fkOwnColumnNames) {
		$this->entityId = $entityId;
		$this->fkParentColumnNames = $fkParentColumnNames;
		$this->fkOwnColumnNames = $fkOwnColumnNames;
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
			new AssociationDefinition($this->entityId, $this->fkParentColumnNames, $this->fkOwnColumnNames),
		];
	}

	public function getParentAssociations() {
		return [];
	}

	public function serialize() {
		return serialize([
			$this->entityId,
			$this->fkParentColumnNames,
			$this->fkOwnColumnNames,
		]);
	}

	public function unserialize($data) {
		list(
			$this->entityId,
			$this->fkParentColumnNames,
			$this->fkOwnColumnNames,
		) = unserialize($data);
	}
}