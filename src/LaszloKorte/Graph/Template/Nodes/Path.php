<?php

namespace LaszloKorte\Graph\Template\Nodes;

use LaszloKorte\Graph\Entity;
use LaszloKorte\Graph\Identifier;
use LaszloKorte\Resource\Query\Record;
use LaszloKorte\Graph\Path\OwnColumnPath;

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

	public function render($link, $record) {
		// HACK
		if(count($this->segments) === 1) {
			$prop = sprintf('foreign_%s_%s', $link->getName(), $this->segments[0]);
			return property_exists($record, $prop) ? $record->$prop : implode('.', $this->segments);
		} else {
			return implode('.', $this->segments);
		}
	}
}