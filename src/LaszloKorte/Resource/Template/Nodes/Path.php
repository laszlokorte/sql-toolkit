<?php

namespace LaszloKorte\Resource\Template\Nodes;

use IteratorAggregate;
use ArrayIterator;

final class Path implements IteratorAggregate {

	private $segments;

	public function __construct(array $segments = []) {
		$this->segments = $segments;
	}

	public function extend($segment) {
		$this->segments [] = $segment;
	}

	public function getIterator() {
		return new ArrayIterator($this->segments);
	}

	public function __toString() {
		return implode('.', $this->segments);
	}
}