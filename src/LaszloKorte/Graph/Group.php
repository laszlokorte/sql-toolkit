<?php

namespace LaszloKorte\Graph;

class Group {
	private $graphDef;
	private $groupId;

	public function __construct($graphDef, $groupId) {
		$this->graphDef = $graphDef;
		$this->groupId = $groupId;
	}

	private function def() {
		return $this->graphDef->getGroup($this->groupId);
	}

	public function entities() {
		return new EntityIterator($this->graphDef, $this->def()->getEntityIds());
	}

	public function title() {
		return $this->def()->getTitle();
	}

	public function hasIcon() {
		return $this->def()->getIcon() !== null;
	}

	public function icon() {
		return $this->def()->getIcon();
	}
}