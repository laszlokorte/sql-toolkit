<?php

namespace LaszloKorte\Resource\Scope;

final class VirtualScope {
	private $realScope;
	private $entity;

	public function __construct(RealScope $realScope, $entity) {
		$this->realScope = $realScope;
		$this->entity = $entity;
	}
}