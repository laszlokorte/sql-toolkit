<?php

namespace LaszloKorte\Graph;

use LaszloKorte\Graph\FieldTypes\FieldType;
use LaszloKorte\Graph\Template\Nodes\Sequence;
use LaszloKorte\Graph\Template\Processed;
use LaszloKorte\Graph\Association\AssociationDefinition;

use Serializable;

final class EntityDefinition implements Serializable {

	private $identifierColumns;
	private $serialColumn;

	private $parentAssociation;

	private $templateSequenceCompiled;
	private $singularName;
	private $pluralName;
	private $isVisible;
	private $description;

	private $displayPaths;


	private $orderColumn;
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

	public function setParent(AssociationDefinition $parentAssociation) {
		$this->parentAssociation = $parentAssociation;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function setDisplayTemplateCompiled(Processed\Sequence $templateSequenceCompiled) {
		$this->templateSequenceCompiled = $templateSequenceCompiled;
	}

	public function setOrderColumn(Identifier $col) {
		$this->orderColumn = $col;
	}

	public function setSearchColumns(array $columnIds) {
		$this->searchColumns = $columnIds;
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

	public function getParentAssociation() {
		return $this->parentAssociation;
	}

	public function getParentId() {
		if($this->parentAssociation) {
			return $this->parentAssociation->getTargetId();
		} else {
			return NULL;
		}
	}

	public function getDisplayTemplateCompiled() {
		return $this->templateSequenceCompiled;
	}

	public function getDisplayPaths() {
		return $this->templateSequenceCompiled->getPaths();
	}

	public function serialize() {
		return serialize([
			$this->identifierColumns,
			$this->serialColumn,
			$this->parentAssociation,
			$this->templateSequenceCompiled,
			$this->singularName,
			$this->pluralName,
			$this->isVisible,
			$this->description,
			$this->displayPaths,
			$this->orderColumn,
			$this->searchColumns,
			$this->iconName,
			$this->fields,
		]);
	}

	public function unserialize($data) {
		list(
			$this->identifierColumns,
			$this->serialColumn,
			$this->parentAssociation,
			$this->templateSequenceCompiled,
			$this->singularName,
			$this->pluralName,
			$this->isVisible,
			$this->description,
			$this->displayPaths,
			$this->orderColumn,
			$this->searchColumns,
			$this->iconName,
			$this->fields,
		) = unserialize($data);
	}
}