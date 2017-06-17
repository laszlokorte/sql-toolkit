<?php

namespace LaszloKorte\Graph\Template\Nodes;

use IteratorAggregate;
use ArrayIterator;
use Serializable;

final class Sequence implements IteratorAggregate, Serializable {

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

	public function serialize() {
		return serialize([
			$this->children,
		]);
	}

	public function unserialize($data) {
		list(
			$this->children,
		) = unserialize($data);
	}
}