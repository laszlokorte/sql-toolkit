<?php

namespace LaszloKorte\Mapper\Relationship;

use LaszloKorte\Mapper\Identifier;

final class OneToManyDefinition {
	private $targetTypeName;

	private $ownKeyColumns;
	private $foreignKeyColumns;

	private $inverseId;

	public function __construct(Identifier $targetTypeName, Identifier $inverseId = NULL) {
		$this->targetTypeName = $targetTypeName;
		$this->inverseId = $inverseId;
	}

	public function setInverse(Identifier $inverseId) {
		$this->inverseId = $inverseId;
	}

	public function getTargetTypeName() {
		return $this->targetTypeName;
	}
}