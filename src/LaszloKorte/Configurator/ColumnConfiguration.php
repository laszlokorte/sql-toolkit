<?php

namespace LaszloKorte\Configurator;

class ColumnConfiguration {
	private $column;
	private $annotations = [];

	public function __construct($column, $annotations) {
		$this->column = $column;
		$this->annotations = $annotations;
	}

	public function getColumn() {
		return $this->column;
	}

	public function getAnnotations() {
		return $this->annotations;
	}
}
