<?php

namespace LaszloKorte\Mapper\Relationship;

use LaszloKorte\Mapper\Identifier;
use LaszloKorte\Mapper\MapperDefinition;

class OneToMany implements Relationship {
	
	private $typeName;
	private $relationshipName;
	private $mapperDefinition;

	public function __construct(Identifier $typeName, Identifier $relationshipName, MapperDefinition $mapperDefinition) {
		$this->typeName = $typeName;
		$this->relationshipName = $relationshipName;
		$this->mapperDefinition = $mapperDefinition;
	}
}