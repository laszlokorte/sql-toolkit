<?php

namespace LaszloKorte\Mapper;

class Mapper {

	private $mapperDefinition;
	private $dataSource;

	public function __construct(MapperDefinition $mapperDefinition, DataSource $dataSource) {
		$this->mapperDefinition = $mapperDefinition;
	}

}
