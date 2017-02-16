<?php

namespace LaszloKorte\Resource\Query;

use LaszloKorte\Graph\Identifier;
use LaszloKorte\Graph\Entity;
use LaszloKorte\Graph\Field;
use LaszloKorte\Graph\Path\OwnColumnPath;
use LaszloKorte\Graph\Path\Path;
use LaszloKorte\Graph\Path\TablePath;
use LaszloKorte\Graph\Association\ParentAssociation;

final class EntityQueryBuilder {

	private $entity;
	private $includeDisplayColumns = FALSE;
	private $includeFieldColumns = FALSE;
	private $sortByField = NULL;
	private $sortOrderAscending = TRUE;

	public function __construct(Entity $entity) {
		$this->entity = $entity;
	}

	public function includeDisplayColumns() {
		$this->includeDisplayColumns = true;
	}

	public function includeFieldColumns() {
		$this->includeFieldColumns = true;
	}

	public function sortByField($fieldName, $asc = TRUE) {
		$this->sortByField = $this->entity->field($fieldName);
		$this->sortOrderAscending = $asc;
	}

	public function sortDefault($asc = TRUE) {
		$this->sortOrderAscending = $asc;
	}

	public function getQuery() {
		$table = $this->entity->id();
		$query = new EntityQuery($table);

		foreach($this->entity->idColumns() AS $idCol) {
			$query->includeColumn(new OwnColumnPath($table, $idCol));
		}

		if($this->entity->serialColumn()) {
			$query->includeColumn(new OwnColumnPath($table, $this->entity->serialColumn()));
		}

		if($this->includeDisplayColumns) {
			// TODO
		}

		if($this->sortByField !== NULL) {
			// TODO
			$query->orderBy(...array_map(function($path) use($table) {
				$dir = $this->sortOrderAscending ? 'ASC' : 'DESC';
				return new Order($path, $dir);
			}, $this->sortPathsForField($table, $this->sortByField)));
		} else {
			$query->orderBy(...array_map(function($idCol) use($table) {
				$dir = $this->sortOrderAscending ? 'ASC' : 'DESC';
				return new Order(new OwnColumnPath($table, $idCol), $dir);
			}, $this->entity->idColumns()));
		}

		if($this->includeFieldColumns) {
			foreach($this->entity->fields() AS $field) {
				foreach($field->relatedColumns() AS $col) {
					$query->includeColumn(new OwnColumnPath($table, $col));
				}

				foreach($field->getChildAssociations() AS $child) {
					$query->includeAggregation(new Aggregation(Aggregation::TYPE_COUNT, $child->getName(), $child->toLink()));
				}

				foreach($field->getParentAssociations() AS $parent) {
					foreach($this->pathFromAssociation($this->entity, $parent) AS $c) {
						$query->includeColumn($c);
					} 
				}
			}
		}
		

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
		if(empty($paths)) {
			return [];
		}

		return array_merge(...array_map(function($p) use ($entity) {
			return $this->expandDisplayPath($entity, $p);
		}, $paths));
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

	private function sortPathsForField(Identifier $table, Field $field) {
		return array_merge(
			// array_map(function($col) use ($table) {
			// 	return new OwnColumnPath($table, $col);
			// }, $field->getChildAssociations()),

			// foreach( AS $child) {
			// 	$query->includeAggregation(new Aggregation(Aggregation::TYPE_COUNT, $field->id(), $child->toLink()));
			// }

			array_merge([], ...array_map(function($parent) use ($table) {
				return $this->pathFromAssociation($this->entity, $parent);
			}, array_values($field->getParentAssociations()))),


			array_map(function($col) use ($table) {
				return new OwnColumnPath($table, $col);
			}, array_values($field->relatedColumns()))
		);
	}
}