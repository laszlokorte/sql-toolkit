<?php

namespace LaszloKorte\Graph\Path;

use LaszloKorte\Graph\Identifier;

class PathLink {
	private $name;

	private $sourceTable;
	private $targetTable;

	private $sourceColumns;
	private $targetColumns;

	public function __construct(Identifier $name, Identifier $sourceTable, Identifier $targetTable, array $sourceColumns, array $targetColumns) {
		if(count($sourceColumns) !== count($targetColumns)) {
			throw new \Exception(sprintf("Column count does not match: source[%s] VS target[%s] - %s", implode(', ', $sourceColumns), implode(', ', $targetColumns), $name));
		}
		foreach($sourceColumns AS $c) {
			if(!$c instanceof Identifier) {
				throw new \Exception("Column name is expected to be an identifier");
			}
		}
		foreach($targetColumns AS $c) {
			if(!$c instanceof Identifier) {
				throw new \Exception("Column name is expected to be an identifier");
			}
		}
		$this->name = $name;
		$this->sourceTable = $sourceTable;
		$this->targetTable = $targetTable;
		$this->sourceColumns = $sourceColumns;
		$this->targetColumns = $targetColumns;
	}

	public function getTarget() {
		return $this->targetTable;
	}

	public function getSource() {
		return $this->sourceTable;
	}

	public function getSourceColumns() {
		return $this->sourceColumns;
	}

	public function getTargetColumns() {
		return $this->targetColumns;
	}

	public function __toString() {
		return (string)$this->name;
	}

	public function getName() {
		return $this->name;
	}
}