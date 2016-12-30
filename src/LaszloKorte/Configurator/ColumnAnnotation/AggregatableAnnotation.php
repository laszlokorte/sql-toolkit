<?php

namespace LaszloKorte\ColumnAnnotation;

class AggregatableAnnotation {
	private $text;

	public function __construct($text) {
		$this->text = $text;
	}
}
