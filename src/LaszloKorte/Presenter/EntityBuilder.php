<?php

namespace LaszloKorte\Presenter;

use LaszloKorte\Configurator\TableAnnotation as TA;
use LaszloKorte\Schema\Table;
use LaszloKorte\Resource\Template\Nodes\Sequence;
use LaszloKorte\Resource\Template\Nodes\OutputTag;

use LaszloKorte\Presenter\Identifier;

final class EntityBuilder {
	private $prevAnnotations = [];
	private $table;
	private $fieldBuilders = [];

	private $id = NULL;
	private $displayTemplate = NULL;
	private $previewUrl = NULL;
	private $description = NULL;
	private $groupName = NULL;
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

	public function setDisplayTemplate(Sequence $template) {
		if(!$this->validTemplate($template)) {
			throw new \Exception(sprintf("Invalid display template for table '%s'", $this->table->getName()));
		}
		$this->displayTemplate = $template;
	}

	public function setPreviewUrl(Sequence $template) {
		if(!$this->validTemplate($template)) {
			throw new \Exception(sprintf("Invalid Preview URL template for table '%s'", $this->table->getName()));
		}
		$this->previewUrl = $template;
	}

	private function validTemplate(Sequence $seq) {
		foreach ($seq as $value) {
			if(!$value instanceof OutputTag) {
				continue;
			}

			$p = array_reduce(iterator_to_array($value->getPath()), function($acc, $segment) {

				if(!$acc instanceof Table) {
					return false;
				}
				if($acc->hasForeignKey($segment)) {
					return $acc->foreignKey($segment)->getTargetTable();
				} elseif($acc->hasColumn($segment)) {
					return true;
				} else {
					return false;
				}
			}, $this->table);

			if($p === false) {
				return false;
			}
		}

		return true;
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
		$this->foreignKeyNames[$fkName] = [
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

	public function buildEntity(ApplicationBuilder $ab, ApplicationDefinition $appDef) {

		$id = new Identifier((string)$this->table->getName());
		$singularTitle = $this->singularTitle;
		$pluralTitle = $this->pluralTitle;
		$idColumns = array_map(function($c) {
			return new Identifier((string) $c->getName());
		}, iterator_to_array($this->table->primaryKeys()));

		$entityDef = $appDef->defineEntity($id, $singularTitle, $pluralTitle, $idColumns);

		$entityDef->setVisibility($this->isVisible);

		if($this->groupName !== NULL) {
			$group = $appDef->putEntityIntoGroup($id, new Identifier($this->groupName), $this->priority);
		}

		if($this->displayTemplate !== NULL) {
			$entityDef->setDisplayTemplate($this->displayTemplate);
		}

		if($this->description !== NULL) {
			$entityDef->setDiscription($this->description);
		}

		if($this->parentName !== NULL) {
			$entityDef->setParent(new Identifier($this->parentName));
		}

		if($this->sortColumn !== NULL) {
			$entityDef->setOrderColumn(new Identifier($this->sortColumn));
		}
	}


}