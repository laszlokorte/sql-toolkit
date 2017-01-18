<?php

namespace LaszloKorte\Presenter;

class Application {
	private $applicationDefinition;

	public function __construct(ApplicationDefinition $def) {
		$this->applicationDefinition = $def;
	}

	public function groups() {
		return new GroupIterator();
	}

	public function entities() {
		return new EntityIterator();
	}
}