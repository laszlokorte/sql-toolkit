<?php

namespace LaszloKorte\Resource\Navigation;

final class Item {
	private $label;
	private $entityId;
	private $active;

	public function __construct($label, $entityId, $active = FALSE) {
		$this->label = $label;
		$this->entityId = $entityId;
		$this->active = $active;
	}

	public function getLabel() {
		return $this->label;
	}

	public function getEntityId() {
		return $this->entityId;
	}

	public function isActive() {
		return $this->active;
	}
}