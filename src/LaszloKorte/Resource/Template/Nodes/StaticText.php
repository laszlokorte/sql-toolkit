<?php

namespace LaszloKorte\Resource\Template\Nodes;

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

	public function render(Record $record, Entity $entity) {
		return $this->text;
	}
}