<?php

namespace LaszloKorte\Presenter;

use LaszloKorte\Configurator\TableAnnotation as TA;
use LaszloKorte\Schema\Table;

use LaszloKorte\Schema\IdentifierMap;

final class EntityBuilder {
	private $prevAnnotations = [];
	private $table;
	private $fieldBuilders = [];

	private $description = NULL;
	private $group = NULL;
	private $hasChildren = true;
	private $parentName = NULL;
	private $priority = 0;
	private $sortColumn = null;
	private $singularTitle = null;
	private $pluralTitle = null;
	private $isVisible = true;
	private $collectionViews = [];
	private $foreignKeyNames = [];
	private $syntheticControls = [];

	public function __construct(Table $table) {
		$this->table = $table;
	}

	public function requireUnique(TA\Annotation $a) {
		$class = get_class($a);
		if(in_array($class, $this->prevAnnotations)) {
			throw new \Exception(sprintf('Duplicate annotation "%s"', $class));
		}

		$this->prevAnnotations []= $class;
	}

	public function attachFieldBuilder(FieldBuilder $fieldBuilder) {
		$this->fieldBuilders []= $fieldBuilder;
	}

	public function setDescription($description) {
		if(!is_string($description)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		$this->description = $description;
	}

	public function setGroup($groupName) {
		if(!is_string($groupName)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		$this->groupName = $groupName;
	}

	public function disableChildren() {
		$this->hasChildren = false;
	}

	public function setParent($parentName) {
		if(!is_string($parentName)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		$this->parentName = $parentName;
	}

	public function setPriority($prio) {
		if(!is_int($prio)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		$this->priority = $prio;
	}

	public function setSortColumn($columnName) {
		if(!is_string($columnName)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		$this->sortColumn = $columnName;
	}

	public function setTitle($singular, $plural) {
		if(!is_string($singular)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		if(!is_string($plural)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		$this->singularTitle = $singular;
		$this->pluralTitle = $plural;
	}

	public function setVisible($isVisible) {
		if(!is_bool($isVisible)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		$this->isVisible = $isVisible;
	}

	public function addCollectionView($name, array $params) {
		if(!is_string($name)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		if(isset($this->collectionViews[$name])) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		$this->collectionViews[$name] = $params;
	}

	public function setForeignKeyName($fkName, $singular, $plural) {
		if(!is_string($fkName)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		if(!is_string($singular)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		if(!is_string($plural)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		if(isset($this->collectionViews[$fkName])) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		$this->collectionViews[$fkName] = [
			'singular' => $singular,
			'plural' => $plural,
		];
	}

	public function addSyntheticControl($type, array $params) {
		if(!is_string($type)) {
			throw new \InvalidArgumentException(__METHOD__);
		}

		$this->syntheticControls []= [
			'type' => $type,
			'params' => $params,
		];
	}

	public function buildEntity(ApplicationDefinition $appDef) {

	}


}