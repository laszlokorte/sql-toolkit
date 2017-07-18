<?php

namespace LaszloKorte\Resource\Navigation;

final class Group {
	private $label;
	private $items;

	public function __construct($label, $items) {
		$this->label = $label;
		$this->items = $items;
	}

	public function getLabel() {
		return $this->label;
	}

	public function getItems() {
		return $this->items;
	}
}