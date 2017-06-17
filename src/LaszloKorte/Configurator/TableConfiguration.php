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

	public function configureColumn(Column $column, array $annotations) {
		$conf = new ColumnConfiguration($column, $annotations);
		$idx = $column->getName();

		if(isset($this->columnConfigurations[$idx])) {
			throw new \Exception('Duplicate configuration for column "%s"', $idx);
		}

		$this->columnConfigurations[$idx] = $conf;

		return $conf;
	}

	public function getColumnConf(Identifier $columnName) {
		return $this->columnConfigurations[$columnName];
	}

	public function getAnnotations() {
		return $this->annotations;
	}
}
