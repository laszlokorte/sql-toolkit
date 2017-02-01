<?php

namespace LaszloKorte\Mapper\Relationship;

use LaszloKorte\Mapper\Identifier;

final class ManyToOneDefinition {

	private $targetTypeName;

	private $foreignKeyColumns;
	
	private $inverseId;

	public function __construct(Identifier $targetTypeName, array $foreignKeyColumns, Identifier $inverseId = NULL) {
		$this->targetTypeName = $targetTypeName;
		$this->foreignKeyColumns = $foreignKeyColumns;
		$this->inverseId = $inverseId;
	}

	public function setInverse(Identifier $inverseId) {
		$this->inverseId = $inverseId;
	}

	public function getTargetTypeName() {
		return $this->targetTypeName;
	}

	public function getKeyColumns() {
		return $this->foreignKeyColumns;
	}
}