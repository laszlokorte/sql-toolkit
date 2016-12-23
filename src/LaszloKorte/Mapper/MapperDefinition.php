<?php

namespace LaszloKorte\Mapper;


class MapperDefinition {

	private $types;

	public function __construct() {
		$this->types = new IdentifierMap();
	}

}
