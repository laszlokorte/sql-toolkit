<?php

namespace LaszloKorte\Mapper\Query\Condition\Value;

use LaszloKorte\Mapper\Record\Record;

final class ConstantValue implements Value {

	private $value;

	public function __construct($value) {
		$this->value = $value;
	}

	public function valueFor(Record $record) {
		return $this->value;
	}

	public function getRootType() {
		return NULL;
	}
}
