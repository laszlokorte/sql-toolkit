<?php

namespace LaszloKorte\Mapper;

use LaszloKorte\Mapper\Collection\LazyCollection;
use LaszloKorte\Mapper\Query\Query;

class Type {
	private $typeName;
	private $mapperDefinition;

	public function __construct(Identifier $typeName, MapperDefinition $mapperDefinition) {
		$this->typeName = $typeName;
		$this->mapperDefinition = $mapperDefinition;
	}

	public function find() {
		return new LazyCollection(new Query($this));
	}

	public function field(Identifier $name) {
		return new Field($this->typeName, $fieldName, $this->mapperDefinition);
	}

	public function rel(Identifier $name) {
		
	}

	public function __get($fieldOrRelationName) {

	}

	public function query(Query $query) {

	}

	public function keyFromValues($values) {

	}
	
}
