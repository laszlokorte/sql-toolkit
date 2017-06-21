<?php

namespace LaszloKorte\Resource\Query;

final class Scope {
	private $pathLinks;
	private $columnNames;

	public function __construct(array $pathLinks, array $columnNames) {
		$this->pathLinks = $pathLinks;
		$this->columnNames = $columnNames;
	}

	public function getLinks() {
		return $this->pathLinks;
	}

	public function getTargetTable() {
		$lastLink = end($this->pathLinks);
		return $lastLink->getTarget();
	}

	public function getColumnNames() {
		return $this->columnNames;
	}
}