<?php

namespace LaszloKorte\Configurator;


use LaszloKorte\Schema\Table;
use LaszloKorte\Schema\IdentifierMap;
use LaszloKorte\Schema\Identifier;

class SchemaConfiguration {
	private $tableConfigurations;

	public function __construct() {
		$this->tableConfigurations = new IdentifierMap();
	}

	public function configureTable(Identifier $tableName, array $annotations) {
		$conf = new TableConfiguration($annotations);

		if(isset($this->columnConfigurations[$tableName])) {
			throw new \Exception('Duplicate configuration for table "%s"', $tableName);
		}

		$this->tableConfigurations[$tableName] = $conf;

		return $conf;
	}

	public function getTableConf(Identifier $tableName) {
		return $this->tableConfigurations[$tableName];
	}
}
