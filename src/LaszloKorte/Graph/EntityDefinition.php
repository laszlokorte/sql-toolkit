<?php

namespace LaszloKorte\Graph;

use LaszloKorte\Graph\FieldTypes\FieldType;
use LaszloKorte\Graph\Template\Nodes\Sequence;
use LaszloKorte\Graph\Template\Processed;

final class EntityDefinition {

	private $identifierColumns;
	private $serialColumn;

	private $parentEntityId;

	private $templateSequence;
	private $singularName;
	private $pluralName;
	private $isVisible;
	private $description;

	private $displayPaths;


	private $orderColumn;
	private $fieldDefinitions;
	private $searchColumns;

	private $iconName;

	private $fields;

	public function __construct($singularName, $pluralName, array $identifierColumns, Identifier $serialColumn = null) {
		$this->singularName = $singularName;
		$this->pluralName = $pluralName;
		$this->identifierColumns = $identifierColumns;
		$this->serialColumn = $serialColumn;
		$this->fields = new IdentifierMap();
	}

	public function defineField(Identifier $id, $title, FieldType $type) {
		if(isset($this->fields[$id])) {
			throw new \Exception(sprintf("Field with id '%s' is already defined for this entity(name: %s).", $id, $this->singularName));
		}
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

	public function setDisplayTemplateCompiled(Processed\Sequence $templateSequenceCompiled) {
		$this->templateSequenceCompiled = $templateSequenceCompiled;
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

		usort($result, function($a,$b) {
			$pA = $this->fields[$a]->getPriority();
			$pB = $this->fields[$b]->getPriority();

			if($pA == $pB) {
				return strcmp($a, $b);
			} else {
				return $pA > $pB ? -1 : 1;
			}
		});

		return $result;
	}

	public function getField(Identifier $id) {
		return $this->fields[$id];
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

	public function getSerialColumn() {
		return $this->serialColumn;
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

	public function getDisplayTemplate() {
		return $this->templateSequence;
	}

	public function getDisplayPaths() {
		return $this->templateSequenceCompiled->getPaths();
	}
}