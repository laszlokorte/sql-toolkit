<?php

namespace LaszloKorte\Resource\Scope;

final class Scope {

	private $entity;

	public function __construct($entity, $choices, $query, $active) {
		$this->entity = $entity;
		$this->choices = $choices;
		$this->query = $query;
		$this->active = $active;
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
}