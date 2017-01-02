<?php

namespace LaszloKorte\Configurator;

use LaszloKorte\Schema\Column;

class TableConfiguration {
	private $columnConfigurations = [];
	private $annotations = [];

	public function __construct() {

	}

	public function configureColumn(Column $column, array $annotations) {
		return new ColumnConfiguration();
	}
}
