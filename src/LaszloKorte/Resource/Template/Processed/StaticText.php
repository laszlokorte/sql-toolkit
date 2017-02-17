<?php

namespace LaszloKorte\Resource\Template\Processed;

use LaszloKorte\Graph\Entity;
use LaszloKorte\Resource\Query\Record;

final class StaticText {

	private $text;

	public function __construct($text) {
		$this->text = $text;
	}

	public function __toString() {
		return $this->text;
	}
}