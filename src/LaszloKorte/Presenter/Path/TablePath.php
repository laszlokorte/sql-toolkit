<?php

namespace LaszloKorte\Presenter\Path;

class TablePath implements Path {
	private $pathLinks;
	private $target;

	public function __construct($pathLink) {
		$this->pathLinks = [$pathLinks];
		$this->target = $pathLink
	}

	public function append(PathLink $link) {
		if($link->getSource() != $this->target->getTarget()) {
			throw new \Exception("Invalid Path");
		}
		$this->pathLinks[] = $link;
	}

	public function length() {
		return count($this->pathLinks);
	}

	public function __toString() {
		return implode('.', $this->pathLinks);
	}
}