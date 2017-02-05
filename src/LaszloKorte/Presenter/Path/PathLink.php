<?php

namespace LaszloKorte\Presenter\Path;

class PathLink {
	private $name;

	private $sourceTable;
	private $targetTable;

	private $sourceColumns;
	private $targetColumns;

	public function __construct($name, $sourceTable, $targetTable, array $sourceColumns, array $targetColumns) {
		if(count($sourceColumns) !== count($targetColumns)) {
			throw new \Exception("Column count does not match");
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

	public function __toString() {
		return $this->name;
	}
}