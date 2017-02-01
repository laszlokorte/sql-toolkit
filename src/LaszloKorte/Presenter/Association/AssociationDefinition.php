<?php

namespace LaszloKorte\Presenter\Association;

final class AssociationDefinition {
	private $targetId;
	private $joinColumns;

	public function __construct($targetId, $joinColumns) {
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