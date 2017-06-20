<?php

namespace LaszloKorte\Resource\Scope;

use LaszloKorte\Graph;
use LaszloKorte\Resource\Scope\Focus;

final class FocusController {
	private $focus;

	public function __construct(Focus $focus, Entity $entity) {
		$this->focus = $focus;
	}

	public function getSegments() {
		return [];
	}
}