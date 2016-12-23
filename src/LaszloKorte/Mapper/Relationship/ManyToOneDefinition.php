<?php

namespace LaszloKorte\Mapper\Relationship;

use LaszloKorte\Mapper\Identifier;

class ManyToOneDefinition {

	private $targetTypeName;

	private $ownKeyColumns;
	private $foreignKeyColumns;
	
	private $inverseId;

	public function __construct(Identifier $targetTypeName) {
		$this->targetTypeName = $targetTypeName;
		$this->inverseId = $inverseId;
	}
}