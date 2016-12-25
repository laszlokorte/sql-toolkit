<?php

namespace LaszloKorte\Mapper\Path;

use LaszloKorte\Mapper\Type;

class RelationshipPath implements Path {
	private $targetType;
	private $relationships = [];

	public function __construct(Type $targetType, array $relationships) {
		$this->targetType = $targetType;
		$this->relationships = $relationships;
	}

	public function __get($name) {

	}

	public function field($name) {

	}

	public function rel($name) {
		
	}

	public function getRootType() {
		
	}
}