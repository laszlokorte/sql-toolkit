<?php

namespace LaszloKorte\Mapper\Relationship;

use LaszloKorte\Mapper\Identifier;

class OneToManyDefinition {
	
	private $targetTypeName;

	private $ownKeyColumns;
	private $foreignKeyColumns;

	private $inverseId;

	public function __construct(Identifier $targetTypeName, Identifier $inverseId = NULL) {
		$this->targetTypeName = $targetTypeName;
		$this->inverseId = $inverseId;
	}
}