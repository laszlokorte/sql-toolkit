<?php

namespace LaszloKorte\Mapper;

use LaszloKorte\Mapper\Collection\LazyCollection;
use LaszloKorte\Mapper\Query\Query;
use LaszloKorte\Mapper\Relationship\ManyToOne;
use LaszloKorte\Mapper\Relationship\OneToMany;

use LaszloKorte\Mapper\Path\OwnFieldPath;
use LaszloKorte\Mapper\Path\RelationshipPath;

final class Type {
	private $typeName;
	private $mapper;

	public function __construct(Identifier $typeName, Mapper $mapper) {
		$this->typeName = $typeName;
		$this->mapper = $mapper;
	}

	public function find() {
		return new LazyCollection(new Query($this));
	}

	public function field(Identifier $name) {
		$def = $this->def();
		if ($def->hasField($name)) {
			return new Field($this->typeName, $name, $this->mapper);
		} else {
			throw new \Exception(sprintf("Type '%s' has no field of name '%s' defined.", $this->typeName, $name));
		}
	}

	public function rel(Identifier $name) {
		$def = $this->def();
		if ($def->hasParentRelationship($name)) {
			return new ManyToOne($this->typeName, $name, $this->mapper);
		} else if ($def->hasChildRelationship($name)) {
			return new OneToMany($this->typeName, $name, $this->mapper);
		} else {
			throw new \Exception(sprintf("Type '%s' has no relationship of name '%s' defined.", $this->typeName, $name));
		}
	}

	public function path(Identifier $fieldOrRelId) {
		$def = $this->def();
		if ($def->hasParentRelationship($fieldOrRelId)) {
			$rel = $this->rel($fieldOrRelId);
			return new RelationshipPath($rel->getTargetType(), [$rel]);
		} else if ($def->hasChildRelationship($fieldOrRelId)) {
			$rel = $this->rel($fieldOrRelId);
			return new RelationshipPath($rel->getTargetType(), [$rel]); 
		} else if ($def->hasField($fieldOrRelId)) {
			return new OwnFieldPath($this, $this->field($fieldOrRelId));
		} else {
			throw new \Exception(sprintf("Type '%s' has no relationship or field of name '%s' defined.", $this->typeName, $fieldOrRelId));
		}
	}

	public function __get($fieldOrRelationName) {
		return $this->path(new Identifier($fieldOrRelationName));
	}

	public function resultForQuery(Query $query) {
		return $this->mapper->resultForQuery($query);
	}

	public function keyFromValues($values) {

	}

	private function def() {
		return $this->mapper->getTypeDefinition($this->typeName);
	}

	public function getTableName() {
		return $this->def()->getTableName();
	}

	public function __toString() {
		return sprintf('%s', $this->typeName);
	}
	
}
