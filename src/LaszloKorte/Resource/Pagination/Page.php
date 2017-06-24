<?php

namespace LaszloKorte\Resource\Pagination;

final class Page {
	private $number;

	public function __construct($number) {
		$this->number = $number;
	}
}