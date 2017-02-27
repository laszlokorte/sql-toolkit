<?php

namespace LaszloKorte\Graph\Template\Processed;

use LaszloKorte\Graph\Template\Renderer;

final class Filter {

	private $name;

	public function __construct($name) {
		$this->name = $name;
	}

	public function __toString() {
		return $this->name;
	}

	public function apply($val, Renderer $renderer) {
		return $renderer->applyFilter($val, $this->name);
	}
}