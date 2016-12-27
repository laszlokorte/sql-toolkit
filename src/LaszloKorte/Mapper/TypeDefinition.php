<?php

namespace LaszloKorte\Mapper;

use LaszloKorte\Mapper\Relationship\OneToManyDefinition;
use LaszloKorte\Mapper\Relationship\ManyToOneDefinition;

final class TypeDefinition {
	private $tableName;
	private $primaryKey;
	private $fields;
	private $childRelationships;
	private $parentRelationships;
	private $validators = [];

	public function __construct(Identifier $tableName) {
		$this->tableName = $tableName;
		$this->fields = new IdentifierMap();
		$this->childRelationships = new IdentifierMap();
		$this->parentRelationships = new IdentifierMap();
	}

	public function getTableName() {
		return $this->tableName;
	}
	
	public function defineField(Identifier $name) {
		$this->requireUnusedIdentifier($name);

		$f = new FieldDefinition($name);
		$this->fields[$name] = $f;
	}

	public function getFieldDefinition(Identifier $name) {
		return $this->fields[$name];
	}

	public function hasField(Identifier $name) {
		return isset($this->fields[$name]);
	}

	public function definePrimaryKey(array $fieldNames) {
		foreach($fieldNames AS $f) {
			if(!$f instanceof Identifier) {
				throw new DefinitionException('Field name of Primary key must be an identifier');
			} else if(!$this->hasField($f)) {
				throw new DefinitionException(sprintf("Type has no field of name '%s' defined", $f));
			}
		}
		$this->primaryKey = $fieldNames;
	}

	public function getPrimaryKey() {
		return $this->primaryKey;
	}
	
	public function defineParentRelationship(Identifier $name, Identifier $targetType = NULL) {
		$this->requireUnusedIdentifier($name);

		$r = new ManyToOneDefinition(
			is_null($targetType) ? $name : $targetType
		);

		$this->parentRelationships[$name] = $r;

		return $r;
	}
	
	public function defineChildRelationship(Identifier $name, Identifier $targetType = NULL) {
		$this->requireUnusedIdentifier($name);

		$r = new OneToManyDefinition(
			is_null($targetType) ? $name : $targetType
		);

		$this->childRelationships[$name] = $r;

		return $r;
	}

	public function getParentRelationshipDefinition(Identifier $name) {
		return $this->parentRelationships[$name];
	}

	public function hasParentRelationship(Identifier $name) {
		return isset($this->parentRelationships[$name]);
	}

	public function getChildRelationshipDefinition(Identifier $name) {
		return $this->childRelationships[$name];
	}

	public function hasChildRelationship(Identifier $name) {
		return isset($this->childRelationships[$name]);
	}

	private function requireUnusedIdentifier(Identifier $id) {
		if(
			isset($this->fields[$id]) ||
			isset($this->childRelationships[$id]) ||
			isset($this->parentRelationships[$id])
		) {
			throw new DefinitionException(sprintf("There is already a field or relationship with id '%s' defined", $id));
		}
	}
}
