<?php

namespace LaszloKorte\Mapper\Path;

use LaszloKorte\Mapper\Type;
use LaszloKorte\Mapper\Identifier;

final class RelationshipPath implements ForeignPath {
	use RelationshipDSLTrait;
	private $targetType;
	private $relationships = [];

	public function __construct(Type $targetType, array $relationships) {
		$this->targetType = $targetType;
		$this->relationships = $relationships;
	}

	public function __get($name) {
		return $this->path(new Identifier($name));
	}

	public function length() {
		return count($this->relationships);
	}

	public function path(Identifier $name) {
		return $this->concatWith($this->targetType->path($name));
	}

	public function field(Identifier $name) {
		return $this->targetType->field($name);
	}

	public function rel(Identifier $name) {
		return $this->targetType->rel($name);
	}

	public function getRootType() {
		return $this->relationships[0]->getSourceType();
	}

	public function getRelationships() {
		return $this->relationships;
	}

	private function concatWith(Path $path) {
		if ($path instanceof FieldPath) {
			return new ForeignFieldPath($this, $path->getField());
		} elseif ($path instanceof RelationshipPath) {
			return new RelationshipPath(
				$path->targetType, 
				array_merge($this->relationships, [end($path->relationships)])
			);
		} else {
			throw new \Exception("Unexpected case");
		}
	}

	public function __toString() {
		return sprintf('%s/%s', $this->relationships[0]->getSourceType(), implode('/', array_map(function($r) {
			return $r->getTargetType();
		}, $this->relationships)));
	}
}