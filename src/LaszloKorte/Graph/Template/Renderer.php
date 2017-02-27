<?php

namespace LaszloKorte\Graph\Template;

interface Renderer {
	public function applyFilter($value, $filter);

	public function unsafeText($string);
}