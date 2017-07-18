<?php

namespace LaszloKorte\Resource\Pagination;

final class Page {
	private $number;

	public function __construct($number) {
		if(!is_int($number)) {
			throw new \Exception("page must be an int");
		}
		$this->number = $number;
	}

	public function offset($perPage) {
		return ($this->number - 1) * $perPage;
	}

	public function getNumber() {
		return $this->number;
	}

	public function __toString() {
		return sprintf('%d', $this->number);
	}
}