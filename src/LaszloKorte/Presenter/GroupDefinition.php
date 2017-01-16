<?php

namespace LaszloKorte\Presenter;

class GroupDefinition {
	private $icon;
	private $title;
	private $entityIds = [];
	private $priority = 0;

	public function __construct() {
	}

	public function setTitle($title) {
		$this->title = $title;
	}

	public function setIcon($iconName) {
		$this->icon = $iconName;
	}

	public function addEntity(Identifier $entityId, $prio = 0) {
		$this->entityIds[] = $entityId;
		$this->priority += $prio;
	}
}