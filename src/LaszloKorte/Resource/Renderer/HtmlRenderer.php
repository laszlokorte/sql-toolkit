<?php

namespace LaszloKorte\Resource\Renderer;

use LaszloKorte\Graph\Template\Renderer;

final class HtmlRenderer implements Renderer {
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
				if(empty($value)) {
					return null;
				}
				return sprintf('<span class="swatch" style="color: %s"></span>', $value);
			case 'raw':
				return html_entity_decode($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
			default:
				return $value;
		}
	}

	public function unsafeText($string) {
		return htmlentities($string, ENT_QUOTES, 'utf-8');
	}
}