<?php

namespace LaszloKorte\Resource\Renderer;

use LaszloKorte\Graph\Template\Renderer;

final class TextRenderer implements Renderer {
	public function applyFilter($value, $filterName) {
		switch($filterName) {
			case 'time':
				if(empty($value)) {
					return null;
				}
				return date('H:i', strtotime($value));
			case 'date':
				if(empty($value)) {
					return null;
				}
				return (new \DateTime($value))->format("d.m.Y");
			case 'color':
				return $value;
			case 'raw':
				return $value;
			default:
				return $value;
		}
	}

	public function unsafeText($string) {
		return $string;
	}
}