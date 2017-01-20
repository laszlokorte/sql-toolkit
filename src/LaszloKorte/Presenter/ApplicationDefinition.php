<?php

namespace LaszloKorte\Presenter;

final class ApplicationDefinition {

	private $entities;
	private $groups;
	private $ungrouped;

	public function __construct() {
		$this->entities = new IdentifierMap();
		$this->groups = new IdentifierMap();
		$this->ungrouped = new IdentifierMap();
	}

	public function defineEntity(Identifier $id, $singular, $plural, $idColumns) {
		$entity = new EntityDefinition($singular, $plural, $idColumns);

		$this->entities[$id] = $entity;
		$this->ungrouped->attach($id);

		return $entity;
	}

	public function putEntityIntoGroup(Identifier $entityId, Identifier $groupId, $prio = 0, $icon = NULL) {
		if(!isset($this->entities[$entityId])) {
			throw new \Exception("Entity is not defined");
		}

		if(!isset($this->groups[$groupId])) {
			$this->groups[$groupId] = new GroupDefinition();
		}

		$this->groups[$groupId]->addEntity($entityId, $prio);
		$this->ungrouped->detach($entityId);

		return $this->groups[$groupId];
	}

	public function defineExporter($name) {

	}

	public function defineAuthentication($userColumn, $passwordColumn) {
		
	}

	public function getEntityIds() {
		$result = [];
		foreach($this->entities AS $id) {
			$result []= $id;
		}
		return $result;
	}

	public function getUngroupedEntityIds() {
		$result = [];
		foreach($this->ungrouped AS $id) {
			$result []= $id;
		}
		return $result;
	}

	public function getEntity(Identifier $id) {
		return $this->entities[$id];
	}

	public function getGroupIds() {
		$result = [];
		foreach($this->groups AS $id) {
			$result []= $id;
		}
		return $result;
	}

	public function getGroup(Identifier $id) {
		return $this->groups[$id];
	}
}