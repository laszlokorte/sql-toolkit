<?php

namespace LaszloKorte\Graph\Template\Processed;

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

	public function render($record, $link = NULL) {
		return htmlentities($this->text, ENT_QUOTES, "UTF-8");
	}
}