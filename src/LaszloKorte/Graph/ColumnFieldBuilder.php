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
			return $this->buildFieldTypeForColumn($this->column, $this->typeName, $this->typeParams);
		} else {
			return $this->defaultTypeForColumn($this->column);
		}
	}

	private function defaultTypeForColumn($column) {
		$columnType = $column->getType();
		$columnName = new Identifier((string) $column->getName());
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
		$columnName = new Identifier((string) $column->getName());

		switch($typeName) {
			case 'choice':
				return new FT\ChoiceField(false, [], $columnName);
			case 'color':
				return new FT\ColorField($columnName);
			case 'date':
				return new FT\DateField($columnName);
			case 'datetime':
				return new FT\DateTimeField($columnName);
			case 'number':
				return new FT\NumberField($columnName, $typeParams['unit']??null);
			case 'password':
				return new FT\PasswordField(false, $columnName);
			case 'syntax':
				return new FT\SyntaxField($typeParams['grammar'] ?? null, $columnName);
			case 'currency':
				return new FT\CurrencyField($typeParams['unit'] ?? null, $columnName);
			case 'text':
				return new FT\TextField(FT\TextField::TYPE_SINGL_LINE, $columnName);
			case 'time':
				return new FT\TimeField(false, $columnName);
			case 'toggle':
				return new FT\ToggleField(FT\ToggleField::TYPE_CHECKBOX, $columnName);
			case 'url':
				return new FT\URLField($columnName);
			case 'email':
				return new FT\EmailField($columnName);
			case 'file':
			case 'geo':
				throw new \Exception(sprintf("Control '%s' can not be used on column '%s'", $typeName, $column));
			default:
				throw new \Exception(sprintf("Unknown control '%s' used for column '%s'", $typeName, $column));
		}
	}

}