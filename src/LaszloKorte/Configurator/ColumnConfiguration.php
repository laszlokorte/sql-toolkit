<?php

namespace LaszloKorte\Configurator;

class ColumnConfiguration {
	private $column;
	private $annotations = [];

	public function __construct($column, $annotations) {
		$this->column = $column;
		$this->annotations = $annotations;
	}
}
