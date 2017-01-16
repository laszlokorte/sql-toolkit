<?php

namespace LaszloKorte\Resource;

use LaszloKorte\Schema\Table;
use LaszloKorte\Schema\ForeignKey;

final class TableRenderer {

	private $table;
	private $scopePath;
	private $excludedKey;

	public function __construct(Table $table, array $scopePath, ForeignKey $excludedKey = NULL) {
		$this->table = $table;
		$this->scopePath = $scopePath;
		$this->excludedKey = $excludedKey;
	}

	public function render($currentQuery) {
		return $this->schema->table($tableName);
	}
}