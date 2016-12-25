<?php

namespace LaszloKorte\Mapper;

use LaszloKorte\Mapper\Relationship\OneToManyDefinition;
use LaszloKorte\Mapper\Relationship\ManyToOneDefinition;

class TypeDefinition {
	private $tableName;
	private $primaryKey;
	private $fields;
	private $relationships;
	private $validators = [];

	public function __construct(Identifier $tableName) {
		$this->tableName = $tableName;
		$this->fields = new IdentifierMap();
		$this->relationships = new IdentifierMap();
	}
	
	public function defineField(Identifier $name) {
		$f = new FieldDefinition($name);
		$this->fields[$name] = $f;
	}

	public function getFieldDefinition(Identifier $name) {
		return $this->fields[$name];
	}
	
	public function defineParentRelationship(Identifier $name, Identifier $targetType = NULL) {
		$r = new ManyToOneDefinition(
			is_null($targetType) ? $name : $targetType
		);

		$this->relationships[$name] = $r;
	}
	
	public function defineChildRelationship(Identifier $name, Identifier $targetType = NULL) {
		$r = new OneToManyDefinition(
			is_null($targetType) ? $name : $targetType
		);

		$this->relationships[$name] = $r;
	}

	public function getRelationshipDefinition(Identifier $name) {
		return $this->relationships[$name];
	}
}
