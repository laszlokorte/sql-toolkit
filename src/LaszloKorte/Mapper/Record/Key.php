<?php

namespace LaszloKorte\Mapper\Record;

final class Key {
	private $type;
	private $keyValues;

	public function __construct(Type $type, $keyValues = NULL) {
		$this->type = type;
		$this->keyValues = $keyValues;
	}
}
