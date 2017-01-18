<?php

namespace LaszloKorte\Presenter;

class Entity {
	private $applicationDefinition;
	private $entityId;

	public function __construct($appDef, $entityId) {

	}

	public function id() {
		
	}

	public function fields() {
		return new FieldIterator();
	}

	public function name($plural = FALSE) {

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