<?php

namespace LaszloKorte\Configurator;

class ColumnConfiguration {
	private $annotations = [];

	public function __construct($annotations) {
		$this->annotations = $annotations;
	}

	public function getAnnotations() {
		return $this->annotations;
	}
}
