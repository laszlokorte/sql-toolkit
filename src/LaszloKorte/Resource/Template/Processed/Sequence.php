<?php

namespace LaszloKorte\Resource\Template\Processed;

use IteratorAggregate;
use ArrayIterator;

final class Sequence implements IteratorAggregate {

	private $children;

	public function __construct(array $children = []) {
		$this->children = $children;
	}

	public function getIterator() {
		return new ArrayIterator($this->children);
	}
}