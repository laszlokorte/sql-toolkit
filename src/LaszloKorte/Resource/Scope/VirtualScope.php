<?php

namespace LaszloKorte\Resource\Scope;

final class VirtualScope {
	private $realScope;
	private $entityId;

	public function __construct(RealScope $realScope, $entityId) {
		$this->realScope = $realScope;
		$this->entityId = $entityId;
	}

	public function getRealScope() {
		return $this->realScope;
	}

	public function isSpecified() {
		return $this->realScope->isSpecified() && $this->entityId !== NULL;
	}

	public function modifyQueryBuilder($queryBuilder) {
		if($this->isSpecified()) {
			$queryBuilder->scope($this->queryScope(), $this->realScope->id($this->entityId));
		}
	}

	private function queryScope() {
		return NULL;
	}

}