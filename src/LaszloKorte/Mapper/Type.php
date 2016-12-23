<?php

namespace LaszloKorte\Mapper;

class Type {
	private $typeName;
	private $mapperDefinition;

	public function __construct(Identifier $typeName, MapperDefinition $mapperDefinition) {
		$this->typeName = $typeName;
		$this->mapperDefinition = $mapperDefinition;
	}

	public function new() {

	}

	public function find() {

	}

	public function field($name) {
		
	}

	public function rel($name) {
		
	}

	public function __get($fieldOrRelationName) {

	}

	public function query(Query $query) {

	}

	public function keyFromValues($values) {

	}
	
}
