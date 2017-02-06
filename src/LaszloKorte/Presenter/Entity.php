<?php

namespace LaszloKorte\Presenter;

class Entity {
	private $appDef;
	private $entityId;

	public function __construct($appDef, Identifier $entityId) {
		$this->appDef = $appDef;
		$this->entityId = $entityId;
	}

	private function def() {
		return $this->appDef->getEntity($this->entityId);
	}

	public function otherEntity(Identifier $id) {
		return new self($this->appDef, $id);
	}

	public function id() {
		return $this->entityId;
	}

	public function fields() {
		return new FieldIterator($this->appDef, $this->entityId, $this->def()->getFieldIds());
	}

	public function field($name) {
		return new Field($this->appDef, $this->entityId, new Identifier($name));
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

	public function parentEntity() {
		return new self($this->appDef, $this->def()->getParentId());
	}

	public function getDisplayTemplate() {
		return $this->def()->getDisplayTemplate();
	}

	public function getDisplayPaths() {
		return $this->def()->getDisplayPaths();
	}
}