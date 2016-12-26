<?php

namespace LaszloKorte\Mapper;

final class Identifier {
	private $name;

	public function __construct($name) {
		if(!is_string($name)) {
			throw new \Exception(sprintf("Identifier must be a string %s given", gettype($name)));
		}
		$this->name = $name;
	}

	public function __toString() {
		return $this->name;
	}

	public function hash() {
		return $this->name;
	}
}