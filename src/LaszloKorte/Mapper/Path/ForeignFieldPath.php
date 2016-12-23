<?php

namespace LaszloKorte\Mapper\Path;

use LaszloKorte\Mapper\Field;

class ForeignFieldPath implements FieldPath {
	private $tablePath;
	private $field;

	public function __construct(TablePath $tablePath, Field $field) {
		$this->tablePath = $tablePath;
		$this->field = $field;
	}
}