<?php

namespace LaszloKorte\Resource\Navigation;

final class Navigation {
	private $groups;

	public function __construct($groups) {
		$this->groups = $groups;
	}

	public function getGroups() {
		return $this->groups;
	}
}