<?php

namespace LaszloKorte\Graph;

use LaszloKorte\Configurator\ColumnAnnotation as CA;
use LaszloKorte\Schema\Column;

use LaszloKorte\Graph\FieldTypes AS FT;
use LaszloKorte\Schema\ColumnType AS CT;

final class ColumnFieldBuilder implements FieldBuilder {
	private $prevAnnotations = [];
	private $name;
	private $column;

	private $isAggregatable = false;
	private $description = null;
	private $visibleInCollection = true;
	private $typeName = null;
	private $typeParams = null;
	private $isLinked = null;
	private $isSecret = false;
	private $title = null;
	private $isVisible = true;
	private $priority = null;
	private $columnIndex;

	private $unknownAnnotations = [];

	public function __construct($columnIndex, $name, Column $column) {
		$this->columnIndex = $columnIndex;
		$this->name = $name;
		$this->column = $column;
	}

	public function increasePriority($prio) {
		$this->priority = ($this->priority ?? 0) + $prio;
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
		$this->typeName = $type;
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
		if($this->column->isSerialColumn()) {
			return;
		}
		$title = $this->title ?? $ab->titelize($this->name);
		$field = $entityDef->defineField(new Identifier($this->name), $title, $this->fieldType());

		$field->setRequired(!$this->column->isNullable());

		if(!is_null($this->description)) {
			$field->setDescription($this->description);
		}

		$field->setPriority($this->priority ?? $this->columnIndex);
		$field->setSecret($this->isSecret);
		$field->setLinked($this->isLinked);
		$field->setVisibility($this->isVisible);
		$field->setCollectionVisibility($this->visibleInCollection);
	}

	public function handlesColumn($columnId) {
		return $this->column->getName() == $columnId;
	}

	private function fieldType() {
		if(isset($this->typeName)) {
			return $this->buildFieldTypeForColumn($this->column, $typeName, $typeParams);
		} else {
			return $this->defaultTypeForColumn();
		}
	}

	private function defaultTypeForColumn() {
		$columnType = $this->column->getType();
		$columnName = new Identifier((string) $this->column->getName());
		switch(get_class($columnType)) {
			case CT\Blob::class:
				if($columnType->isBinary()) {
					return new FT\TextField(FT\TextField::TYPE_MULTI_LINE, $columnName);
				} else {
					return new FT\TextField(FT\TextField::TYPE_MULTI_LINE, $columnName);
				}
			case CT\Chars::class:
				return new FT\TextField(FT\TextField::TYPE_SINGLE_LINE, $columnName);
			case CT\Date::class:
				return new FT\DateField($columnName);
			case CT\DateTime::class:
				return new FT\DateTimeField($columnName);
			case CT\Decimal::class:
				return new FT\NumberField($columnName);
			case CT\Enum::class:
				return new FT\ChoiceField($columnType->allowsMultiple(), $columnType->getOptions(), $columnName);
			case CT\Floating::class:
				return new FT\NumberField($columnName);
			case CT\Integer::class:
				return new FT\NumberField($columnName);
			case CT\Time::class:
				return new FT\TimeField(false, $columnName);
			case CT\TimeStamp::class:
				return new FT\TextField(FT\TextField::TYPE_SINGLE_LINE, $columnName);
			case CT\Year::class:
				return new FT\TextField(FT\TextField::TYPE_SINGLE_LINE, $columnName);
			default:
				return new FT\TextField(FT\TextField::TYPE_SINGLE_LINE, $columnName);
		}
	}

	public function buildFieldTypeForColumn($column, $typeName, $typeParams) {
		switch($typeName) {
			case 'choice':
			case 'color':
			case 'date':
			case 'datetime':
			case 'file':
			case 'geo':
			case 'number':
			case 'password':
			case 'sort':
			case 'syntax':
			case 'text':
			case 'time':
			case 'toggle':
			default:
				throw new \Exception(sprintf("Unknown control '%s' used for column '%s'", $typeName, $column));
		}
	}

}