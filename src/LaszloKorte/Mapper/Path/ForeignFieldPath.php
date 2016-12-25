<?php

namespace LaszloKorte\Mapper\Path;

use LaszloKorte\Mapper\Field;

class ForeignFieldPath implements FieldPath {
	private $relationshipPath;
	private $field;

	public function __construct(RelationshipPath $relationshipPath, Field $field) {
		$this->relationshipPath = $relationshipPath;
		$this->field = $field;
	}

	public function getRootType() {
		
	}
}