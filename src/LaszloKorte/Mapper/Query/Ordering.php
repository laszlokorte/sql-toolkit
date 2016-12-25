<?php

namespace LaszloKorte\Mapper\Query;

class Ordering {
	const ASC = 'DIRECTION_ASC';
	const DESC = 'DIRECTION_DESC';
	
	private $path;
	private $direction;

	public function __construct($path, $direction = Ordering::ASC) {
		$this->path = $path;
		$this->direction = $direction;
	}

	public function getRootType() {
		return $this->getRootType();
	}
}
