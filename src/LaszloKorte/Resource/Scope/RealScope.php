<?php

namespace LaszloKorte\Resource\Scope;

final class RealScope {
	private $focus;
	private $data;
	private $query;
	private $namingConvention;

	public function __construct($focus, $data, $query, $namingConvention) {
		$this->focus = $focus;
		$this->data = $data;
		$this->query = $query;
		$this->namingConvention = $namingConvention;
	}
}