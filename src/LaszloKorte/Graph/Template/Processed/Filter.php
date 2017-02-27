<?php

namespace LaszloKorte\Graph\Template\Processed;

final class Filter {

	private $name;

	public function __construct($name) {
		$this->name = $name;
	}

	public function __toString() {
		return $this->name;
	}

	public function apply($val) {
		switch($this->name) {
			case 'time':
				if(is_null($val)) {
					return null;
				}
				return date('H:i', strtotime($val));
			case 'date':
				if(is_null($val)) {
					return null;
				}
				return (new \DateTime($val))->format("d.m.Y");
			case 'color':
				if(is_null($val)) {
					return null;
				}
				return sprintf('<span class="swatch" style="color: %s"></span>', $val);
			case 'raw':
				return html_entity_decode($val, ENT_QUOTES | ENT_XML1, 'UTF-8');
			default:
				return $val;
		}
	}
}