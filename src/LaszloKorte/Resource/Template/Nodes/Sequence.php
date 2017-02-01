<?php

namespace LaszloKorte\Resource\Template\Nodes;

use IteratorAggregate;
use ArrayIterator;

final class Sequence implements IteratorAggregate {

	private $children;

	public function __construct(array $children = []) {
		$this->children = $children;
	}

	public function append($node) {
		$this->children[] = $node;
	}

	public function getIterator() {
		return new ArrayIterator($this->children);
	}

	public function __toString() {
		return implode('', $this->children);
	}
}