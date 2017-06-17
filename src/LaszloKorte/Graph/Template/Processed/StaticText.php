<?php

namespace LaszloKorte\Graph\Template\Processed;

use LaszloKorte\Graph\Template\Renderer;

use LaszloKorte\Graph\Entity;
use LaszloKorte\Resource\Query\Record;

use Serializable;

final class StaticText implements Serializable {

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

	public function serialize() {
		return serialize([
			$this->text,
		]);
	}

	public function unserialize($data) {
		list(
			$this->text,
		) = unserialize($data);
	}
}