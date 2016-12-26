<?php

namespace LaszloKorte\Mapper\Query;

use LaszloKorte\Mapper\Record\Record;
use LaszloKorte\Mapper\Path\FieldPath;

class Ordering {
	const ASC = 'DIRECTION_ASC';
	const DESC = 'DIRECTION_DESC';
	
	private $path;
	private $direction;

	public function __construct(FieldPath $path, $direction = Ordering::ASC) {
		$this->path = $path;
		$this->direction = $direction;
	}

	public function getRootType() {
		return $this->getRootType();
	}

	public function evalFor(Record $a, Record $b) {

	}
}
