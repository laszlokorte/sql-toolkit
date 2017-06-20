<?php

namespace LaszloKorte\Graph;

use LaszloKorte\Graph\Identifier;
use LaszloKorte\Graph\Path\OwnColumnPath;
use LaszloKorte\Graph\Association\ParentAssociation;
use LaszloKorte\Graph\Tree\Chain;


class Entity {
	private $graphDef;
	private $entityId;

	public function __construct(GraphDefinition $graphDef, Identifier $entityId) {
		$this->graphDef = $graphDef;
		$this->entityId = $entityId;
	}

	private function def() {
		return $this->graphDef->getEntity($this->entityId);
	}

	public function otherEntity(Identifier $id) {
		return new self($this->graphDef, $id);
	}

	public function id() {
		return $this->entityId;
	}

	public function fields() {
		return new FieldIterator($this->graphDef, $this->entityId, $this->def()->getFieldIds());
	}

	public function field($name) {
		return new Field($this->graphDef, $this->entityId, new Identifier($name));
	}

	public function title($plural = FALSE) {
		return $this->def()->getName($plural);
	}

	public function icon() {
		return $this->def()->icon();
	}

	public function isVisible() {
		return $this->def()->isVisible();
	}

	public function description() {
		return $this->def()->getDescription();
	}

	public function isSearchable() {
		return !empty($this->def()->getSearchColumns());
	}

	public function isSortable() {
		return $this->def()->getOrderColumn() !== null;
	}

	public function isIdentifiable() {
		return !empty($this->def()->getIdColumns());
	}

	public function idColumns() {
		return $this->def()->getIdColumns();
	}

	public function serialColumn() {
		return $this->def()->getSerialColumn();
	}

	public function parentEntity() {
		$parentId = $this->def()->getParentId();
		if($parentId !== NULL) {
			return new self($this->graphDef, $parentId);
		} else {
			return NULL;
		}
	}

	public function parentAssociation() {
		$assocDef = $this->def()->getParentAssociation();
		if($assocDef === NULL) {
			return NULL;
		} else {
			return new ParentAssociation($this->graphDef, $this->entityId, new Identifier(sprintf('parent_%s_%s', $this->entityId, $assocDef->getTargetId())), $assocDef);
		}
	}

	public function hasParentEntity() {
		return $this->def()->getParentId() !== NULL;
	}

	public function getTreeChain() {
		$entity = $this;
		$segments = [];
		while($entity->hasParentEntity()) {
			$parentAssoc = $entity->parentAssociation();
			$entity = $parentAssoc->getTargetEntity();
			$segments[] = $parentAssoc;
		}

		return new Chain($this->graphDef, $this->entityId, array_reverse($segments));
	}

	public function getDisplayTemplateCompiled() {
		return $this->def()->getDisplayTemplateCompiled();
	}

	public function getDisplayPaths() {
		return $this->def()->getDisplayPaths();
	}

	public function column(Identifier $columnId) {
		return new OwnColumnPath($this->entityId, $columnId);
	}

	public function __toString() {
		return sprintf('%s', $this->entityId);
	}
}