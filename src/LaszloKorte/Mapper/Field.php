<?php

namespace LaszloKorte\Mapper;

class Field {
	private $typeName;
	private $fieldName;
	private $mapperDefinition;

	public function __construct(Identifier $typeName, Identifier $fieldName, MapperDefinition $mapperDefinition) {
		$this->typeName = $typeName;
		$this->fieldName = $fieldName;
		$this->mapperDefinition = $mapperDefinition;
	}
}
