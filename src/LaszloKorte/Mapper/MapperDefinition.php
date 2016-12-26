<?php

namespace LaszloKorte\Mapper;


class MapperDefinition {

	private $types;

	public function __construct() {
		$this->types = new IdentifierMap();
	}

	public function defineType(Identifier $name) {
		$t = new TypeDefinition($name);

		$this->types[$name] = $t;

		return $t;
	}

	public function getTypeDefinition(Identifier $name) {
		return $this->types[$name];
	}

	public function hasType(Identifier $name) {
		return isset($this->types[$name]);
	}
}
