<?php

namespace LaszloKorte\Mapper\Path;

use LaszloKorte\Mapper\Field;

final class ForeignFieldPath implements FieldPath, ForeignPath {
	use FieldDSLTrait;
	private $relationshipPath;
	private $field;

	public function __construct(RelationshipPath $relationshipPath, Field $field) {
		$this->relationshipPath = $relationshipPath;
		$this->field = $field;
	}

	public function getRootType() {
		
	}

	public function isParentPath() {
		return $this->relationshipPath->isParentPath();
	}

	public function length() {
		return $this->relationshipPath->length() + 1;
	}

	public function getRelationships() {
		return $this->relationshipPath->getRelationships();
	}

	public function getField() {
		return $this->field;
	}

	public function __toString() {
		return sprintf('%s/%s', $this->relationshipPath, $this->field);
	}
}