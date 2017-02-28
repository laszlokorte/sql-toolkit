<?php

namespace LaszloKorte\Schema;

use Serializable;

final class IndexDefinition implements Serializable {
	const TYPE_UNIQUE = 'UNIQUE';
	const TYPE_KEY = 'KEY';
	const TYPE_FULLTEXT = 'FULLTEXT';

	private $type;
	private $columnNames;

	public function __construct($type, $columnNames) {
		$allowedTypes = [self::TYPE_UNIQUE, self::TYPE_KEY, self::TYPE_FULLTEXT];

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

	public function serialize() {
		return serialize([
			$this->type,
			$this->columnNames,
		]);
	}

	public function unserialize($data) {
		list(
			$this->type,
			$this->columnNames,
		) = unserialize($data);
	}
}