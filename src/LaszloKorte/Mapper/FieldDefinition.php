<?php

namespace LaszloKorte\Mapper;

final class FieldDefinition {
	private $columnName;
	private $validators = [];

	public function __construct(Identifier $columnName) {
		$this->columnName = $columnName;
	}
}
