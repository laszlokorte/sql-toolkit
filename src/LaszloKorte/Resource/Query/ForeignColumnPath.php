<?php

namespace LaszloKorte\Resource\Query;

use LaszloKorte\Schema\Column;

final class ForeignKeyPath {

	private $fkPath;

	public function __construct(ForeignKeyPath $fkPath, Column $column) {
		$this->fkPath = $fkPath;
	}

	public function length() {
		return count($this->foreignKeys);
	}

	public function __toString() {
		return "no Implemented";
	}
}