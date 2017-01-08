<?php

namespace LaszloKorte\Resource\Template\Nodes;

final class Sequence {

	private $children;

	public function __construct(array $children = []) {
		$this->children = $children;
	}

	public function append($node) {
		$this->children[] = $node;
	}
}