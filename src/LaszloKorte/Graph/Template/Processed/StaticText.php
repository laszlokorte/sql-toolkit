<?php

namespace LaszloKorte\Graph\Template\Processed;

use LaszloKorte\Graph\Template\Renderer;

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

	public function render($record, Renderer $renderer, $link = NULL) {
		return $renderer->unsafeText($this->text);
	}
}