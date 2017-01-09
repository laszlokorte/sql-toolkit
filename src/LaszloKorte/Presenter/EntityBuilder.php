<?php

namespace LaszloKorte\Presenter;

use LaszloKorte\Configurator\TableAnnotation as TA;
use LaszloKorte\Schema\Table;

final class EntityBuilder {
	private $prevAnnotations = [];
	private $table;

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

	}

	public function setDescription($description) {
		
	}

	public function setGroup($groupName) {
		
	}

	public function disableChildren() {
		
	}

	public function setParent($parentName) {

	}

	public function setPriority($prio) {

	}

	public function setSortColumn($columnName) {

	}

	public function setTitle($singular, $plural) {

	}

	public function setVisible($isVisible) {

	}

	public function addCollectionView($name) {

	}

	public function setForeignKeyName($fkName, $singular, $plural) {

	}

	public function addSyntheticControl($name, $params) {

	}


}