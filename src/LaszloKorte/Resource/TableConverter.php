<?php

namespace LaszloKorte\Resource;

use LaszloKorte\Schema\Schema;

final class TableConverter {

    private $schema;

    public function __construct(Schema $schema) {
        $this->schema = $schema;
    }

	public function convert($tableName) {
		return $this->schema->table($tableName);
	}
}