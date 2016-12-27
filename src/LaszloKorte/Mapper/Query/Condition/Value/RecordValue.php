<?php

namespace LaszloKorte\Mapper\Query\Condition\Value;

use LaszloKorte\Mapper\Record\Record;
use LaszloKorte\Mapper\Path\FieldPath;

final class RecordValue implements Value {

	private $path;

	public function __construct(FieldPath $path) {
		$this->path = $path;
	}

	public function valueFor(Record $record) {
	
	}

	public function getRootType() {
		return $this->path->getRootType();
	}

	public function getPaths() {
		return [$this->path];
	}
}
