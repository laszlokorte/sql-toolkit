<?php

namespace LaszloKorte\Configurator;


use LaszloKorte\Schema\Table;

class SchemaConfiguration {
	private $tableConfigurations = [];

	public function __construct() {

	}

	public function configureTable(Table $table, array $annotations) {
		return new TableConfiguration();
	}
}
