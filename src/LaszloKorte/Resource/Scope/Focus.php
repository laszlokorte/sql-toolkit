<?php

namespace LaszloKorte\Resource\Scope;

final class Focus {
	private $entity; // day
	private $id; // 4

	public function __construct(Entity $entity, $id) {
		$this->entity = $entity;
		$this->id = $id;
	}
}