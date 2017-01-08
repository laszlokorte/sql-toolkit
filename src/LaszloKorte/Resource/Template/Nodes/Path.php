<?php

namespace LaszloKorte\Resource\Template\Nodes;

final class Path {

	private $segments;

	public function __construct(array $segments = []) {
		$this->segments = $segments;
	}

	public function extend($segment) {
		$this->segments [] = $segment;
	}
}