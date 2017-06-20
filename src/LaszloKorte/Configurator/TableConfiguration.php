<?php

namespace LaszloKorte\Configurator;

use LaszloKorte\Schema\Column;
use LaszloKorte\Schema\IdentifierMap;
use LaszloKorte\Schema\Identifier;

class TableConfiguration {
	private $columnConfigurations = [];
	private $annotations = [];

	public function __construct(array $annotations) {
		$this->annotations = $annotations;
		$this->columnConfigurations = new IdentifierMap();
	}

	public function configureColumn(Identifier $columnName, array $annotations) {
		$conf = new ColumnConfiguration($annotations);

		if(isset($this->columnConfigurations[$columnName])) {
			throw new \Exception('Duplicate configuration for column "%s"', $columnName);
		}

		$this->columnConfigurations[$columnName] = $conf;

		return $conf;
	}

	public function getColumnConf(Identifier $columnName) {
		return $this->columnConfigurations[$columnName];
	}

	public function getAnnotations() {
		return $this->annotations;
	}
}
