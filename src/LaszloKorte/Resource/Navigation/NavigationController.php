<?php

namespace LaszloKorte\Resource\Navigation;

final class NavigationController {
	private $graph;

	public function __construct($graph) {
		$this->graph = $graph;
	}

	public function getNavigation($entityId, $parameters, $realScope) {
		return new Navigation(array_merge(array_map(function($group) use ($entityId) {
			return new Group($group->title(), array_map(function($entity) use ($entityId) {
				return new Item($entity->title(), $entity->id(), $entity->id() == $entityId);
			}, iterator_to_array($group->entities())));
		}, iterator_to_array($this->graph->groups())), $this->graph->hasUngroupedEntities() ? [
			new Group("Ungrouped", array_map(function($entity) use ($entityId) {
				return new Item($entity->title(), $entity->id(), $entity->id() == $entityId);
			}, iterator_to_array($this->graph->ungroupedEntities())))
		] : []));
	}
}