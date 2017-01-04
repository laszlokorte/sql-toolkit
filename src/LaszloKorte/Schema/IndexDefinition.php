<?php

namespace LaszloKorte\Schema;

final class IndexDefinition {
	const TYPE_UNIQUE = 'UNIQUE';
	const TYPE_KEY = 'KEY';

	private $type;
	private $columnNames;

	public function __construct($type, $columnNames) {
		$allowedTypes = [self::TYPE_UNIQUE, self::TYPE_KEY];

		if(!in_array($type, $allowedTypes)) {
			throw new \Exception(sprintf("Invalid type %s for Index. Expected one of (%s)", $type, implode(', ', $allowedTypes)));
		}

		$this->type = $type;
		$this->columnNames = $columnNames;
	}

	public function getType() {
		return $this->type;
	}

	public function getColumnNames() {
		return $this->columnNames;
	}
}