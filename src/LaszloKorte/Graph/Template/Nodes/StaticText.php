<?php

namespace LaszloKorte\Graph\Template\Nodes;

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

	public function getText() {
		return $this->text;
	}

	public function render($link, $record) {
		return $this->text;
	}
}