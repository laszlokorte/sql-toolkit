<?php

namespace LaszloKorte\Resource\Template\Nodes;

final class Filter {

	private $name;

	public function __construct($name) {
		$this->name = $name;
	}
}