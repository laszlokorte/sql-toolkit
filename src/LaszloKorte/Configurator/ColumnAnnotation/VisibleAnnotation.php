<?php

namespace LaszloKorte\ColumnAnnotation;

class VisibleAnnotation {
	private $isVisible;

	public function __construct($isVisible) {
		$this->isVisible = $isVisible;
	}
}
