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
				return date('G:i', strtotime($val));
			case 'date':
				if(is_null($val)) {
					return null;
				}
				return (new \DateTime($val))->format("d.m.Y");
			default:
				return $val;
		}
	}
}