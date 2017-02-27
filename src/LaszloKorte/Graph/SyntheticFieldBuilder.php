<?php

namespace LaszloKorte\Graph;

use LaszloKorte\Configurator\ColumnAnnotation as CA;
use LaszloKorte\Schema\Column;

use LaszloKorte\Graph\FieldTypes AS FT;
use LaszloKorte\Graph\Identifier;

final class SyntheticFieldBuilder implements FieldBuilder {
	private $prevAnnotations = [];
	private $name;
	private $columns;

	private $isAggregatable = false;
	private $description = null;
	private $visibleInCollection = true;
	private $typeName = null;
	private $typeParams = null;
	private $isLinked = null;
	private $isSecret = false;
	private $title = null;
	private $isVisible = true;

	private $unknownAnnotations = [];

	public function __construct($table, $name, $typeName, $columns, $typeParams) {
		$this->table = $table;
		$this->name = $name;
		$this->typeName = $typeName;
		$this->columns = $columns;
		$this->typeParams = $typeParams;
	}

	public function reportUnknownAnnotation($annotation) {
		$this->unknownAnnotations[] = $annotation;
	}

	public function requireUnique(CA\Annotation $a) {
		$class = get_class($a);
		if(in_array($class, $this->prevAnnotations)) {
			throw new \Exception(sprintf('Duplicate annotation "%s"', $class));
		}

		$this->prevAnnotations []= $class;
	}

	public function setAggregatable($isAggregatable) {
		if(!is_bool($isAggregatable)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		$this->isAggregatable = $isAggregatable;
	}

	public function setDescription($description) {
		if(!is_string($description)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		$this->description = $description;
	}

	public function setCollectionVisible($visible) {
		if(!is_bool($visible)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		$this->visibleInCollection = $visible;
	}

	public function setType($type, array $params) {
		if(!is_string($type)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		$this->type = $type;
		$this->typeParams = $params;
	}

	public function setLinked($isLinked) {
		if(!is_bool($isLinked)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		$this->isLinked = $isLinked;
	}

	public function setSecret($isSecret) {
		if(!is_bool($isSecret)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		$this->isSecret = $isSecret;
	}

	public function setTitle($title) {
		if(!is_string($title)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		$this->title = $title;
	}

	public function setVisible($isVisible) {
		if(!is_bool($isVisible)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		$this->isVisible = $isVisible;
	}

	public function buildField($ab, $entityBuilder, $entityDef) {
		$title = $this->title ?? $ab->titelize($this->name);
		$field = $entityDef->defineField(new Identifier($this->name), $title, $this->fieldType());

		$field->setRequired(array_reduce($this->columns, function($acc, $c) {
			return $acc || !$c->isNullable();
		}), false);

		if(!is_null($this->description)) {
			$field->setDescription($this->description);
		}

		$field->setPriority($this->priority ?? 5);
		$field->setSecret($this->isSecret);
		$field->setLinked($this->isLinked);
		$field->setVisibility($this->isVisible);
		$field->setCollectionVisibility($this->visibleInCollection);
	}

	public function increasePriority($prio) {
		$this->priority = ($this->priority ?? 0) + $prio;
	}

	public function handlesColumn($columnId) {
		return array_reduce($this->columns, function($acc, $col) use ($columnId) {
			return $acc || $col->getName() == $columnId;
		}, false);
	}

	private function fieldType() {
		switch($this->typeName) {
			case 'file':
				return new FT\FileField(
					$this->typeParams['dir'], 
					new Identifier((string)$this->columns[0]->getName()), 
					new Identifier((string)$this->columns[1]->getName()), 
					new Identifier((string)$this->columns[2]->getName())
				);
			case 'geo':
				return new FT\GeoField(
					new Identifier((string)$this->columns[0]->getName()), 
					new Identifier((string)$this->columns[1]->getName())
				);
			default:
				throw new \Exception(sprintf("Unknown control '%s'", $this->typeName));
		}
	}

}