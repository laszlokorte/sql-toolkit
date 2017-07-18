<?php

namespace LaszloKorte\Resource\Scope;

final class ScopeSelector {

	private $query;

	public function __construct($query) {
		$this->query = $query;
	}

	public function getQuery() {
		return $this->query;
	}

	public function isAvailable() {
		return false;
	}

}