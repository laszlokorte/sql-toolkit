<?php

namespace LaszloKorte\ColumnAnnotation;

class DescriptionAnnotation {
	private $text;

	public function __construct($text) {
		$this->text = $text;
	}
}
