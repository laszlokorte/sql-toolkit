<?php

namespace LaszloKorte\Graph;

class Field {
	private $resource;

	public function __construct(Resource $resource) {
		$this->resource = $resource;
	}
}