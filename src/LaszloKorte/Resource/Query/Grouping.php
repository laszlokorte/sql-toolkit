<?php

namespace LaszloKorte\Resource\Query;

final class Grouping {
	private $pathLinks;

	public function __construct(array $pathLinks) {
		$this->pathLinks = $pathLinks;
	}

	public function getLinks() {
		return $this->pathLinks;
	}

	public function getTargetTable() {
		$lastLink = end($this->pathLinks);
		return $lastLink->getTarget();
	}

	public function getTargetColumns() {
		$lastLink = end($this->pathLinks);
		return $lastLink->getTargetColumns();
	}
}