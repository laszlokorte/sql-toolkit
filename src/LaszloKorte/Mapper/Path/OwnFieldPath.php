<?php

namespace LaszloKorte\Mapper\Path;

use LaszloKorte\Mapper\Field;
use LaszloKorte\Mapper\Type;

class OwnFieldPath implements FieldPath {
	private $targetType;
	private $field;

	public function __construct(Type $targetType, Field $field) {
		$this->targetType = $targetType;
		$this->field;
	}
}