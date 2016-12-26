<?php

namespace LaszloKorte\Mapper\Path;

use LaszloKorte\Mapper\Field;

final class ForeignFieldPath implements FieldPath {
	use FieldDSLTrait;
	private $relationshipPath;
	private $field;

	public function __construct(RelationshipPath $relationshipPath, Field $field) {
		$this->relationshipPath = $relationshipPath;
		$this->field = $field;
	}

	public function getRootType() {
		
	}

	public function getField() {
		return $this->field;
	}
}