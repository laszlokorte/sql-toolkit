<?php

namespace LaszloKorte\Resource\Query;

use LaszloKorte\Schema\ForeignKey;

final class ForeignKeyPath {

    private $foreignKeys;

    public function __construct(array $foreignKeys) {
        $this->foreignKeys = $foreignKeys;
    }

	public function length() {
		return count($this->foreignKeys);
	}

	public function __toString() {
		return "no Implemented";
	}
}