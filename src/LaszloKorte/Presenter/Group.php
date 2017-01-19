<?php

namespace LaszloKorte\Presenter;

class Group {
	private $appDef;
	private $groupId;

	public function __construct($appDef, $groupId) {
		$this->appDef = $appDef;
		$this->groupId = $groupId;
	}

	private function def() {
		return $this->appDef->getGroup($this->groupId);
	}

	public function entities() {
		return new EntityIterator($this->appDef, $this->def()->getEntityIds());
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