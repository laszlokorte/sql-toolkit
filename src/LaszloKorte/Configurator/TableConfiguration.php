<?php

namespace LaszloKorte\Configurator;

use LaszloKorte\Schema\Column;
use LaszloKorte\Schema\Table;
use LaszloKorte\Schema\IdentifierMap;
use LaszloKorte\Schema\Identifier;

class TableConfiguration {
	private $table;
	private $columnConfigurations = [];
	private $annotations = [];

	public function __construct(Table $table, array $annotations) {
		$this->table = $table;
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

	public function getColumnIds() {
		$result = [];
		foreach($this->columnConfigurations AS $c) {
			$result[] = $c;
		}

		return $result;
	}

	public function getColumnConf(Identifier $id) {
		return $this->columnConfigurations[$id];
	}

	public function getTable() {
		return $this->table;
	}

	public function getAnnotations() {
		return $this->annotations;
	}
}
