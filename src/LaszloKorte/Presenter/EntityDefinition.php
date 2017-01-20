<?php

namespace LaszloKorte\Presenter;

use LaszloKorte\Presenter\FieldTypes\FieldType;
use LaszloKorte\Resource\Template\Nodes\Sequence;

final class EntityDefinition {

	private $identifierColumns;

	private $parentEntityId;

	private $templateSequence;
	private $singularName;
	private $pluralName;
	private $isVisible;
	private $description;

	private $orderColumn;
	private $fieldDefinitions;
	private $searchColumns;

	private $iconName;

	private $fields;

	public function __construct($singularName, $pluralName, array $identifierColumns) {
		$this->singularName = $singularName;
		$this->pluralName = $pluralName;
		$this->identifierColumns = $identifierColumns;
		$this->fields = new IdentifierMap();
	}

	public function defineField(Identifier $id, $title, FieldType $type) {
		$field = new FieldDefinition($title, $type);

		$this->fields[$id] = $field;

		return $field;
	}

	public function setVisibility($isVisible) {
		$this->isVisible = $isVisible;
	}

	public function setParent(Identifier $parentEntityId) {
		$this->parentEntityId = $parentEntityId;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function setDisplayTemplate(Sequence $templateSequence) {
		$this->templateSequence = $templateSequence;
	}

	public function setOrderColumn(Identifier $col) {
		$this->orderColumn = $col;
	}

	public function setSearchColumns(array $columnIds) {
		$this->searchColumns = $col;
	}

	public function setIcon($name) {
		$this->iconName = $name;
	}

	public function getFieldIds() {
		$result = [];
		foreach($this->fields AS $id) {
			$result []= $id;
		}
		return $result;
	}

	public function getName($plural = false) {
		if($plural) {
			return $this->pluralName;
		} else {
			return $this->singularName;
		}
	}

	public function getDescription() {
		return $this->description;
	}

	public function getIdColumns() {
		return $this->identifierColumns;
	}

	public function getOrderColumn() {
		return $this->orderColumn;
	}

	public function getSearchColumns() {
		return $this->searchColumns;
	}

	public function isVisible() {
		return $this->isVisible;
	}

	public function getIcon() {
		return $this->icon;
	}

	public function getParentId() {
		return $this->parentEntityId;
	}
}