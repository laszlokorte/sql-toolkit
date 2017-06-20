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
		return count($this->segments) + 1;
	}

	public function getIterator() {
		return new ArrayIterator(array_map(function($i) {
			return new ChainLink($this, $i);
		}, range(0, max(0, $this->length() - 1))));
	}

	public function getRoot() {
		if(count($this->segments) > 0) {
			$target = $this->segments[0]->getTargetId();
			return new self($this->graphDef, $target, []);
		} else {
			return $this;
		}
	}

	public function intersectWith(Chain $other) {
		if($this->graphDef !== $other->graphDef) {
			throw new \Exception("xxx");
		}

		$segmentsA = $this->getIterator();
		$segmentsB = $other->getIterator();
		$segments = [];
		$target = NULL;

		for ($i = 0, $max=min(count($segmentsA), count($segmentsB)) - 1; $i < $max; $i++) { 
			$ownParent = $segmentsA[$i]->target()->id();
			$otherParent = $segmentsB[$i]->target()->id();
			if($ownParent == $otherParent) {
				if($i > 0) {
					$segments[] = $segmentsA[$i]->target()->parentAssociation();
				}
			} else {
				$target = $segmentsA[$i]->target()->id();
				break;
			}
		}

		if ($target === NULL) {
			$target = $this->targetEntityId;	
		}
		return new Chain($this->graphDef, $target, $segments);
	}
}