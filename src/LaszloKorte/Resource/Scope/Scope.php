<?php

namespace LaszloKorte\Resource\Scope;

final class Scope {

	private $entity;

	public function __construct($entity, $record, $choices, $query, $active) {
		$this->entity = $entity;
		$this->choices = $choices;
		$this->query = $query;
		$this->active = $active;
		$this->record = $record;
	}

	public function record() {
		return $this->record;
	}

	public function getEntity() {
		return $this->entity;
	}

	public function getChoices() {
		return $this->choices;
	}

	public function getQuery() {
		return $this->query;
	}

	public function __toString() {
		return $this->entity->title();
	}

	public function parent() {
		return $this->entity->parentEntity();
	}
}