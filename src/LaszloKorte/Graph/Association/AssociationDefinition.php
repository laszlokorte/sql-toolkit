<?php

namespace LaszloKorte\Graph\Association;

use LaszloKorte\Graph\Identifier;

final class AssociationDefinition {
	private $targetId;
	private $joinColumns;

	public function __construct(Identifier $targetId, array $joinColumns) {
		foreach($joinColumns AS $c) {
			if(!$c instanceof Identifier) {
				throw new \Exception(sprintf('Join column is expected to be an %s but %s given', Identifier::class, get_class($c)));
			}
		}
		$this->targetId = $targetId;
		$this->joinColumns = $joinColumns;
	}

	public function getTargetId() {
		return $this->targetId;
	}

	public function getJoinColumns() {
		return $this->joinColumns;
	}
}