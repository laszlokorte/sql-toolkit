<?php

namespace LaszloKorte\Mapper;

final class Field {
	private $typeName;
	private $fieldName;
	private $mapper;

	public function __construct(Identifier $typeName, Identifier $fieldName, Mapper $mapper) {
		$this->typeName = $typeName;
		$this->fieldName = $fieldName;
		$this->mapper = $mapper;
	}
}
