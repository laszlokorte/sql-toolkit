<?php

namespace LaszloKorte\Mapper\Relationship;

use LaszloKorte\Mapper\Identifier;
use LaszloKorte\Mapper\Mapper;

final class OneToMany implements Relationship {
	
	private $typeName;
	private $relationshipName;
	private $mapper;

	public function __construct(Identifier $typeName, Identifier $relationshipName, Mapper $mapper) {
		$this->typeName = $typeName;
		$this->relationshipName = $relationshipName;
		$this->mapper = $mapper;
	}

	public function getName() {
		return $this->relationshipName;
	}

	public function getTargetType() {
		return $this->mapper->type($this->def()->getTargetTypeName());
	}

	public function getSourceType() {
		return $this->mapper->type($this->typeName);
	}

	public function getSourceKeys() {
		return $this->mapper->getTypeDefinition($this->typeName)->getPrimaryKey();
	}

	public function getTargetKeys() {
		return $this->def()->getKeyColumns();
	}

	private function def() {
		return $this->mapper->getTypeDefinition($this->typeName)->getChildRelationshipDefinition($this->relationshipName);
	}

	public function __toString() {
		return sprintf('[%s->%s](%s)', $this->getSourceType(), $this->getTargetType(), $this->relationshipName);
	}
}