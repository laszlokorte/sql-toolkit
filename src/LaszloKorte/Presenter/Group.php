<?php

namespace LaszloKorte\Presenter;

class Group {
	private $applicationDefinition;
	private $groupId;

	public function __construct($appDef, $groupId) {

	}

	public function entities() {
		return new EntityIterator();
	}

	public function title() {
		
	}
}