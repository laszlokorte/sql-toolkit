<?php

namespace LaszloKorte\Presenter;

class Entity {
	private $appDef;
	private $entityId;

	public function __construct($appDef, $entityId) {
		$this->appDef = $appDef;
		$this->entityId = $entityId;
	}

	private function def() {
		return $this->appDef->getEntity($this->entityId);
	}

	public function id() {
		return $this->entityId;
	}

	public function fields() {
		return new FieldIterator($this->appDef, $this->entityId, $this->def()->getFieldIds());
	}

	public function title($plural = FALSE) {
		return $this->def()->getName($plural);
	}

	public function icon() {

	}

	public function isVisible() {

	}

	public function description() {

	}

	public function isSearchable() {

	}

	public function isSortable() {

	}

	public function isIdentifiable() {

	}

	public function idColumns() {

	}

	public function parentEntity() {

	}
}