<?php

namespace LaszloKorte\Graph\Template\Nodes;

use LaszloKorte\Graph\Entity;
use LaszloKorte\Graph\Identifier;
use LaszloKorte\Resource\Query\Record;
use LaszloKorte\Graph\Path\OwnColumnPath;

use IteratorAggregate;
use ArrayIterator;
use Serializable;

final class Path implements IteratorAggregate, Serializable {

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

	public function serialize() {
		return serialize([
			$this->segments
		]);
	}

	public function unserialize($data) {
		list(
			$this->segments,
		) = unserialize($data);
	}
}