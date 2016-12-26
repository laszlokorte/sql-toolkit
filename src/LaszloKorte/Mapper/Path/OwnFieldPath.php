<?php

namespace LaszloKorte\Mapper\Path;

use LaszloKorte\Mapper\Field;
use LaszloKorte\Mapper\Type;

final class OwnFieldPath implements FieldPath {
	use FieldDSLTrait;
	private $targetType;
	private $field;

	public function __construct(Type $targetType, Field $field) {
		$this->targetType = $targetType;
		$this->field;
	}

	public function getRootType() {
		
	}

	public function getField() {
		return $this->field;
	}
}