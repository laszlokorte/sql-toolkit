<?php

namespace LaszloKorte\Mapper;

class TypeDefinition {
	private $tableName;
	private $primaryKey;
	private $fields;
	private $relationships;
	private $validators = [];

	public function __construct($tableName) {
		$this->tableName = $tableName;
		$this->fields = new IdentifierMap();
		$this->relationships = new IdentifierMap();
	}
	
}
