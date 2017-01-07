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

	public function configureTable(Table $table, array $annotations) {
		$conf = new TableConfiguration($table, $annotations);
		$idx = $table->getName();

		if(isset($this->columnConfigurations[$idx])) {
			throw new \Exception('Duplicate configuration for table "%s"', $idx);
		}

		$this->tableConfigurations[$idx] = $conf;

		return $conf;
	}

	public function getTableIds() {
		$result = [];
		foreach($this->tableConfigurations AS $t) {
			$result[] = $t;
		}

		return $result;
	}

	public function getTableConf(Identifier $id) {
		return $this->tableConfigurations[$id];
	}
}
