<?php

namespace LaszloKorte\Presenter;

use LaszloKorte\Configurator\ColumnAnnotation as CA;
use LaszloKorte\Schema\ForeignKey;

use LaszloKorte\Presenter\FieldTypes\RefChildrenField;
use LaszloKorte\Presenter\FieldTypes\RefParentField;

final class RelationFieldBuilder implements FieldBuilder {
	private $prevAnnotations = [];
	private $foreignKey;
	private $reversed;

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

	public function __construct(ForeignKey $foreignKey, $reversed) {
		$this->foreignKey = $foreignKey;
		$this->reversed = $reversed;
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

	public function buildField($ab, $entityDef) {
		$id = 'rel_' . ($this->reversed ? $ab->pluralize((string) $this->foreignKey->getName()) : (string) $this->foreignKey->getName());
		$title = $this->title ?? $ab->titelize($this->nameFromFk($ab));
		if($this->reversed) {
			$type = new RefChildrenField();
		} else {
			$type = new RefParentField();
		}
		$field = $entityDef->defineField(new Identifier($id), $title, $type);

		$field->setRequired(!$this->reversed && array_reduce(iterator_to_array($this->foreignKey->getOwnColumns()), function($acc, $col) {
			return $acc && !$col->isNullable();
		}, true));

		if(!is_null($this->description)) {
			$field->setDescription($this->description);
		}

		$field->setVisibility($this->isVisible);
		$field->setCollectionVisibility($this->visibleInCollection);
	}

	private function nameFromFk($ab) {
		$fkName = (string) $this->foreignKey->getName();

		if($this->reversed) {
			if(preg_match('/^fk_((?:_?[^_])+)__(?:.+)$/', $fkName, $match)) {
				return $ab->pluralize($match[1]);
			} else {
				return (string) $ab->pluralize($this->foreignKey->getOwnTable()->getName());
			}
			
		} else {
			if(preg_match('/^fk_(?:_?[^_])+__(.+)$/', $fkName, $match)) {
				return $match[1];
			} else {
				return $fkName;
			}
		}
	}

}