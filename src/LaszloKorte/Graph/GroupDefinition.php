<?php

namespace LaszloKorte\Graph;

class GroupDefinition {
	private $icon;
	private $title;
	private $entityIds = [];
	private $entityPrios = [];
	private $priority = 0;

	public function __construct() {
	}

	public function setTitle($title) {
		$this->title = $title;
	}

	public function setIcon($iconName) {
		$this->icon = $iconName;
	}

	public function getTitle() {
		return $this->title;
	}

	public function getIcon() {
		return $this->icon;
	}

	public function addEntity(Identifier $entityId, $prio = 0) {
		$this->entityIds[] = $entityId;
		$this->entityPrios[] = $prio;
		$this->priority += $prio;
	}

	public function getEntityIds() {
		$result = $this->entityIds;

		uksort($result, function($a, $b) {
			$pA = $this->entityPrios[$a];
			$pB = $this->entityPrios[$b];

			if($pA === $pB) {
				return strcmp($this->entityIds[$pA], $this->entityIds[$pB]);
			} else {
				return $pA < $pB ? 1 : -1;
			}
		});

		return $result;
	}

	public function getPriority() {
		return $this->priority;
	}
}