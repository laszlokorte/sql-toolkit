<?php

namespace LaszloKorte\Resource\Query;

use LaszloKorte\Presenter\Entity;
use LaszloKorte\Presenter\Path\OwnColumnPath;
use LaszloKorte\Presenter\Path\Path;
use LaszloKorte\Presenter\Path\TablePath;
use LaszloKorte\Presenter\Association\ParentAssociation;

final class EntityQueryBuilder {

	public function __construct() {
	}

	public function queryForEntity(Entity $entity) {
		$table = $entity->id();
		$query = new EntityQuery($table);

		foreach($entity->idColumns() AS $idCol) {
			$query->includeColumn(new OwnColumnPath($table, $idCol));
		}

		foreach($entity->fields() AS $field) {
			foreach($field->relatedColumns() AS $col) {
				$query->includeColumn(new OwnColumnPath($table, $col));
			}

			foreach($field->getChildAssociations() AS $child) {

			}

			foreach($field->getParentAssociations() AS $parent) {
				foreach($this->pathFromAssociation($entity, $parent) AS $c) {
					$query->includeColumn($c);
				} 
			}

			$paths = array_merge($field->getChildAssociations(), $field->getParentAssociations());
			echo implode("<br>\n\n", $paths);
			if($paths) {
				echo "<br>";
			}

		}

		echo implode("<br/>", $entity->getDisplayPaths());

		return $query;
	}

	private function pathFromAssociation($entity, ParentAssociation $assoc) {
		$target = $assoc->getTargetEntity();

		$base = new TablePath($assoc->toLink());

		$displayPaths = array_map(function($p) use ($base) {
			return $p->relativeTo($base);
		}, $this->expandDisplayPaths($entity, $target->getDisplayPaths()));

		return $displayPaths;
	}

	private function expandDisplayPaths($entity, $paths) {
		$paths = array_map(function($p) use ($entity) {
			return $this->expandDisplayPath($entity, $p);
		}, $paths);

		if(empty($paths)) {
			return [];
		}
		
		return array_merge(...$paths);
	}

	private function expandDisplayPath($entity, Path $base) {
		if($base instanceof TablePath) {
			$otherEnt = $entity->otherEntity($base->getTarget());
			return array_map(function($p) use ($base) {
				return $p->relativeTo($base);
			}, $this->expandDisplayPaths($otherEnt, $otherEnt->getDisplayPaths()));
		} else {
			return [$base];
		}
	}
}