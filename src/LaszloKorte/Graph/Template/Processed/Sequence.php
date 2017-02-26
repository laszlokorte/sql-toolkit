<?php

namespace LaszloKorte\Graph\Template\Processed;

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

	public function getPaths() {
		$result = [];
		foreach ($this->children as $c) {
			if($c instanceof ColumnValue) {
				$result[] = $c->getPath();
			}
		}

		return $result;
	}
}