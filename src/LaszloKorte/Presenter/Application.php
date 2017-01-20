<?php

namespace LaszloKorte\Presenter;

class Application {
	private $applicationDefinition;

	public function __construct(ApplicationDefinition $def) {
		$this->applicationDefinition = $def;
	}

	public function groups() {
		$groupIds = $this->applicationDefinition->getGroupIds();
		return new GroupIterator($this->applicationDefinition, $groupIds);
	}

	public function entities() {
		$entityIds = $this->applicationDefinition->getEntityIds();

		return new EntityIterator($this->applicationDefinition, $entityIds);
	}

	public function ungroupedEntities() {
		$entityIds = $this->applicationDefinition->getUngroupedEntityIds();

		return new EntityIterator($this->applicationDefinition, $entityIds);
	}

	public function hasUngroupedEntities() {
		return !empty($this->applicationDefinition->getUngroupedEntityIds());
	}
}