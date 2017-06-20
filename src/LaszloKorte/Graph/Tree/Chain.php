<?php

namespace LaszloKorte\Graph\Tree;

use LaszloKorte\Graph\Identifier;
use LaszloKorte\Graph\GraphDefinition;
use LaszloKorte\Graph\Entity;

use Countable;
use IteratorAggregate;
use ArrayIterator;

final class Chain implements IteratorAggregate {
	private $graphDef;
	private $targetEntityId;
	private $segments;

	public function __construct(GraphDefinition $graphDef, Identifier $targetEntityId, array $segments) {
		$this->graphDef = $graphDef;
		$this->targetEntityId = $targetEntityId;
		$this->segments = $segments;
	}

	public function getSegment($index) {
		if(!isset($this->segments[$index])) {
			throw new \Exception();
			
		}
		return $this->segments[$index];
	} 

	public function getTarget() {
		return new Entity($this->graphDef, $this->targetEntityId);
	}

	public function getParentChain() {
		if(count($this->segments) > 0) {
			$segments = $this->segments;
			$target = array_pop($segments)->getTargetId();
			return new self($this->graphDef, $target, $segments);
		} else {
			return NULL;
		}
	}

	public function length() {
		return count($this->segments);
	}

	public function getIterator() {
		return new ArrayIterator(array_map(function($i) {
			return new ChainLink($this, $i);
		}, range(0, $this->length())));
	}
}