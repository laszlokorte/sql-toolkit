<?php

namespace LaszloKorte\Graph\Template\Processed;

use LaszloKorte\Graph\Template\Renderer;

use Serializable;

final class Filter implements Serializable {

	private $name;

	public function __construct($name) {
		$this->name = $name;
	}

	public function __toString() {
		return $this->name;
	}

	public function apply($val, Renderer $renderer) {
		return $renderer->applyFilter($val, $this->name);
	}

	public function serialize() {
		return serialize([
			$this->name,
		]);
	}

	public function unserialize($data) {
		list(
			$this->name,
		) = unserialize($data);
	}
}