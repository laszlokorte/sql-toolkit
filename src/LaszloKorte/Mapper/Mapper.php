<?php

namespace LaszloKorte\Mapper;

class Mapper {

	private $mapperDefinition;
	private $dataSource;

	public function __construct(MapperDefinition $mapperDefinition, DataSource $dataSource) {
		$this->mapperDefinition = $mapperDefinition;
	}

	public function type(Identifier $name) {
		return new Type($name, $this);
	}

	public function __get($name) {
		return $this->type(new Identifier($name));
	}

}
