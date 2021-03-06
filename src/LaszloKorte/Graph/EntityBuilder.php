<?php

namespace LaszloKorte\Graph;

use LaszloKorte\Configurator\TableAnnotation as TA;
use LaszloKorte\Schema\Table;
use LaszloKorte\Graph\Template\Nodes\Sequence;
use LaszloKorte\Graph\Template\Nodes\StaticText;
use LaszloKorte\Graph\Template\Nodes\Path;
use LaszloKorte\Graph\Template\Nodes\OutputTag;
use LaszloKorte\Graph\Template\Processed;
use LaszloKorte\Graph\Association\AssociationDefinition;

use LaszloKorte\Graph\Identifier;

final class EntityBuilder {
	private $prevAnnotations = [];
	private $table;
	private $fieldBuilders = [];

	private $id = NULL;
	private $displayTemplate = NULL;
	private $displayTemplateCompiled;
	private $previewUrl = NULL;
	private $description = NULL;
	private $groupName = NULL;
	private $hasChildren = true;
	private $parent = NULL;
	private $priority = 0;
	private $sortColumn = null;
	private $singularTitle = null;
	private $pluralTitle = null;
	private $isVisible = true;
	private $collectionViews = [];
	private $foreignKeyNames = [];
	private $unknownAnnotations = [];


	public function __construct(Table $table) {
		$this->table = $table;
	}

	public function getTable() {
		return $this->table;
	}

	public function reportUnknownAnnotation($annotation) {
		$this->unknownAnnotations[] = $annotation;
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

	public function setDisplayTemplateCompiled(Processed\Sequence $template) {
		$this->displayTemplateCompiled = $template;
	}

	public function setPreviewUrl(Sequence $template) {
		$this->previewUrl = $template;
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

	public function setId($id) {
		if(!is_string($id)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		$this->id = $id;
	}

	public function setParent(AssociationDefinition $parent) {
		$this->parent = $parent;
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

		if(!$this->table->foreignKeys()->contains($fkName) && !$this->table->reverseForeignKeys()->contains($fkName)) {
			throw new \InvalidArgumentException(__METHOD__);
		}

		$this->foreignKeyNames[$fkName] = (object)[
			'singular' => $singular,
			'plural' => $plural,
		];
	}

	public function buildEntity(GraphBuilder $ab, GraphDefinition $appDef) {

		$id = new Identifier((string)$this->table->getName());
		$singularTitle = $this->singularTitle ?? $ab->titelize((string)$id);
		$pluralTitle = $this->pluralTitle ?? $ab->pluralize($singularTitle);
		$idColumns = array_map(function($c) {
			return new Identifier((string) $c->getName());
		}, iterator_to_array($this->table->primaryKeys()));

		$entityDef = $appDef->defineEntity($id, $singularTitle, $pluralTitle, $idColumns, $this->table->getSerialColumn() ? new Identifier((string) $this->table->getSerialColumn()) : null);

		$entityDef->setVisibility($this->isVisible);

		if($this->groupName !== NULL) {
			$group = $appDef->putEntityIntoGroup($id, new Identifier($this->groupName), $this->priority);
			$group->setTitle($ab->titelize($this->groupName));
		}

		$displayTemplate = $this->displayTemplateCompiled ?? new Processed\Sequence();

		$entityDef->setDisplayTemplateCompiled($displayTemplate);

		if($this->description !== NULL) {
			$entityDef->setDescription($this->description);
		}

		if($this->parent !== NULL) {
			$entityDef->setParent($this->parent);
		}

		if($this->sortColumn !== NULL) {
			$entityDef->setOrderColumn(new Identifier($this->sortColumn));
		}

		foreach($this->fieldBuilders AS $fieldBuilder) {
			$fieldBuilder->buildField($ab, $this, $entityDef);
		}

		foreach($this->table->indices() as $index) {
			if(!$index->isFulltext()) {
				continue;
			}

			$entityDef->setSearchColumns(array_map(function($col) {
				return new Identifier((string)$col->getName());
			}, iterator_to_array($index->getColumns())));

			break;
		}
	}

	public function isColumnAlreadyHandled($columnId) {
		if($this->sortColumn == $columnId) {
			return true;
		}
		
		foreach($this->fieldBuilders AS $fb) {
			if($fb->handlesColumn($columnId)) {
				return true;
			}
		}

		return false;
	}

	public function getForeignKeyName($fkId, $singular = TRUE) {
		if($singular) {
			return $this->foreignKeyNames[$fkId]->singular ?? NULL;
		} else {
			return $this->foreignKeyNames[$fkId]->plural ?? NULL;
		}
	}


}