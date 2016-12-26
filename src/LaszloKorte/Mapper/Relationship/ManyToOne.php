<?php

namespace LaszloKorte\Mapper\Relationship;

use LaszloKorte\Mapper\Identifier;
use LaszloKorte\Mapper\Mapper;

class ManyToOne implements Relationship {
	
	private $typeName;
	private $relationshipName;
	private $mapper;

	public function __construct(Identifier $typeName, Identifier $relationshipName, Mapper $mapper) {
		$this->typeName = $typeName;
		$this->relationshipName = $relationshipName;
		$this->mapper = $mapper;
	}

	public function getTargetType() {
		return $this->mapper->type($this->typeName)->rel($this->relationshipName);
	}

	public function getSourceType() {
		return $this->mapper->type($this->typeName);
	}
}