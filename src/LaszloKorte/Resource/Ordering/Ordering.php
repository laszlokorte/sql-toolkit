<?php

namespace LaszloKorte\Resource\Ordering;

final class Ordering {
	private $field;
	private $direction;

	public function __construct($field, $direction) {
		$this->field = $field;
		$this->direction = $direction;
	}
}