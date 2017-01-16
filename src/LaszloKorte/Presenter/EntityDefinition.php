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


	public function __construct($singularName, $pluralName, array $identifierColumns) {
		$this->singularName = $singularName;
		$this->pluralName = $pluralName;
		$this->identifierColumns = $identifierColumns;
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
}