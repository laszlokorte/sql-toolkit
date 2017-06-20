<?php

namespace LaszloKorte\Graph\Tree;

use LaszloKorte\Graph\Identifier;
use LaszloKorte\Graph\GraphDefinition;
use LaszloKorte\Graph\Entity;

final class ChainLink {
	public function __construct($chain, $index) {
		$this->chain = $chain;
		$this->index = $index;
	}

	public function isRoot() {
		return $this->index === 0;
	}

	public function isLast() {
		return $this->index === $this->chain->length();
	}

	public function source() {
		if($this->isRoot()) {
			return null;
		} else {
			return $this->chain->getSegment($this->index)->getTargetEntity();
		}
	}

	public function target() {
		if($this->isLast()) {
			return $this->chain->getTarget();
		} else {
			return $this->chain->getSegment($this->index)->getTargetEntity();
		}
	}
}