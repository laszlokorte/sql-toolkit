<?php

namespace LaszloKorte\Mapper\Relationship;

use LaszloKorte\Mapper\Identifier;
use LaszloKorte\Mapper\Mapper;

final class ManyToOne implements Relationship {
	
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

	private function def() {
		return $this->mapper->getTypeDefinition($this->typeName)->getParentRelationshipDefinition($this->relationshipName);
	}

	public function __toString() {
		return sprintf('[%s->%s](%s)', $this->getSourceType(), $this->getTargetType(), $this->relationshipName);
	}
}