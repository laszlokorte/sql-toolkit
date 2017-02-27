<?php

namespace LaszloKorte\Graph;

class Graph {
	private $graphDefinition;

	public function __construct(GraphDefinition $def) {
		$this->graphDefinition = $def;
	}

	public function groups() {
		$groupIds = $this->graphDefinition->getGroupIds();
		
		return new GroupIterator($this->graphDefinition, $groupIds);
	}

	public function entities() {
		$entityIds = $this->graphDefinition->getEntityIds();

		return new EntityIterator($this->graphDefinition, $entityIds);
	}

	public function entity($name) {
		return new Entity($this->graphDefinition, new Identifier($name));
	}

	public function hasEntity($name) {
		return $this->graphDefinition->hasEntity(new Identifier($name));
	}

	public function ungroupedEntities() {
		$entityIds = $this->graphDefinition->getUngroupedEntityIds();

		return new EntityIterator($this->graphDefinition, $entityIds);
	}

	public function hasUngroupedEntities() {
		return !empty($this->graphDefinition->getUngroupedEntityIds());
	}

	public function getAuthenticators() {
		return $this->graphDefinition->getAuthenticators();
	}
}