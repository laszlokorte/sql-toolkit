<?php

namespace LaszloKorte\Graph\Template\Nodes;

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

	public function getText() {
		return $this->text;
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