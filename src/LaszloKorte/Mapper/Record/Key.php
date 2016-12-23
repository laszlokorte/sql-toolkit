<?php

namespace LaszloKorte\Mapper\Record;

class Key {
	private $values;
	private $keyValues;

	public function __construct(Type $type, $keyValues = NULL) {
		$this->type = type;
		$this->keyValues = $keyValues;
	}
}
