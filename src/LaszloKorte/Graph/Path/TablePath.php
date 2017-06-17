<?php

namespace LaszloKorte\Graph\Path;

use Serializable;

class TablePath implements Path, Serializable {
	private $pathLinks;
	private $target;

	public function __construct($pathLink) {
		$this->pathLinks = [$pathLink];
		$this->target = $pathLink;
	}

	public function getTarget() {
		return $this->target->getTarget();
	}

	public function getLinks() {
		return $this->pathLinks;
	}

	public function append(PathLink $link) {
		if($link->getSource() != $this->target->getTarget()) {
			throw new \Exception("Invalid Path");
		}
		$this->pathLinks[] = $link;
		$this->target = $link;
	}

	public function length() {
		return count($this->pathLinks);
	}

	public function __toString() {
		return implode('.', $this->pathLinks);
	}

	public function relativeTo(TablePath $p) {
		$newPath = clone $p;
		foreach($this->pathLinks AS $l) {
			$newPath->append($l);
		}

		return $newPath;
	}

	public function serialize() {
		return serialize([
			$this->pathLinks,
			$this->target,
		]);
	}

	public function unserialize($data) {
		list(
			$this->pathLinks,
			$this->target,
		) = unserialize($data);
	}
}