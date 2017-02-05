<?php

namespace LaszloKorte\Presenter\Path;

class OwnColumnPath implements Path {
	private $tableName;
	private $columnName;

	public function __construct($tableName, $columnName) {
		$this->tableName = $tableName;
		$this->columnName = $columnName;
	}

	public function length() {
		return 1;
	}

	public function __toString() {
		return "self.$this->columnName";
	}
}