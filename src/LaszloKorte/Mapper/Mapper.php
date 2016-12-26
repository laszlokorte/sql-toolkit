<?php

namespace LaszloKorte\Mapper;

use LaszloKorte\Mapper\DataSource\DataSource;
use LaszloKorte\Mapper\Query\Query;

final class Mapper {

	private $mapperDefinition;
	private $dataSource;

	public function __construct(MapperDefinition $mapperDefinition, DataSource $dataSource) {
		$this->mapperDefinition = $mapperDefinition;
		$this->dataSource = $dataSource;
	}

	public function type(Identifier $name) {
		if(!$this->mapperDefinition->hasType($name)) {
			throw new \Exception(sprintf("Type '%s' is not defined", $name));
		}
		return new Type($name, $this);
	}

	public function __get($name) {
		return $this->type(new Identifier($name));
	}

	public function getTypeDefinition(Identifier $name) {
		return $this->mapperDefinition->getTypeDefinition($name);
	}

	public function resultForQuery(Query $query) {
		return $this->dataSource->resultFor($query);
	}

}
