<?php

namespace LaszloKorte\Mapper\Property;

class FieldDefinition {
	private $columnName;
	private $validators = [];

	public function __construct(Identifier $columnName) {
		$this->columnName = $columnName;
	}
}
