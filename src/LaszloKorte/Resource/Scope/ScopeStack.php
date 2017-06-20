<?php

namespace LaszloKorte\Resource\Scope;

use LaszloKorte\Resource\Scope\Focus;

final class ScopeStack {
	private $focus;
	private $data;

	public function __construct(Focus $focus, $data) {
		$this->focus = $focus;
		$this->data = $data;
	}
}