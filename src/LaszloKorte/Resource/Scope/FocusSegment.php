<?php

namespace LaszloKorte\Resource\Scope;

final class FocusSegment {
	private $entity;

	public function __construct(Entity $entity) {
		$this->entity = $entity;
	}
}