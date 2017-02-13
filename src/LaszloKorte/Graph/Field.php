<?php

namespace LaszloKorte\Graph;

use LaszloKorte\Graph\Association;

class Field {
	private $graphDef;
	private $entityId;
	private $fieldId;

	public function __construct($graphDef, $entityId, $fieldId) {
		$this->graphDef = $graphDef;
		$this->entityId = $entityId;
		$this->fieldId = $fieldId;
	}

	private function def() {
		return $this->graphDef->getEntity($this->entityId)->getField($this->fieldId);
	}

	public function id() {
		return $this->fieldId;
	}

	public function entity() {
		return new Entity($this->graphDef, $this->entityId);
	}

	public function title() {
		return $this->def()->getTitle();
	}

	public function isRequired() {
		return $this->def()->isRequired();
	}

	public function description() {
		return $this->def()->getDescription();
	}

	public function type() {
		return $this->def()->getType();
	}

	public function typeTemlate() {
		return $this->type()->getTemplateName();
	}

	public function isVisible() {
		return $this->def()->isVisible();
	}

	public function isVisibleInCollection() {
		return $this->def()->isVisibleInCollection();
	}

	public function isSecret() {
		return $this->def()->isSecret();
	}

	public function relatedColumns() {
		return $this->type()->getRelatedColumns();
	}

	public function getPriority() {
		return $this->def()->getPriority();
	}

	public function getChildAssociations() {
		return array_map(function($assoc) {
			return new Association\ChildAssociation($this->graphDef, $this->entityId, $this->fieldId, $assoc);
		}, $this->type()->getChildAssociations());
	}

	public function getParentAssociations() {
		return array_map(function($assoc) {
			return new Association\ParentAssociation($this->graphDef, $this->entityId, $this->fieldId, $assoc);
		}, $this->type()->getParentAssociations());
	}
}