<?php

namespace LaszloKorte\Graph\Tree;

use LaszloKorte\Graph\Identifier;
use LaszloKorte\Graph\GraphDefinition;
use LaszloKorte\Graph\Entity;
use LaszloKorte\Graph\Path\TablePath;

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

	public function backLinks() {
		$max=$this->chain->length(); 
		if($this->index === 0) {
			return null;
		}
		$links = [$this->chain->getSegment($this->index - 1)->toLink()];
		for ($i=$this->index;
			$i < $max; $i++) { 
			$links[] = $this->chain->getSegment($i)->toLink();
		}

		return array_reverse($links);
	}
}