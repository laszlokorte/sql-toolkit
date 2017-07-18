<?php

namespace LaszloKorte\Resource\Scope;

final class Focus {
	private $entityId;
	private $recordId;

	public function __construct($entityId, $recordId) {
		$this->entityId = $entityId;
		$this->recordId = $recordId;
	}

	public function getEntityId() {
		return $this->entityId;
	}

	public function getRecordId() {
		return $this->recordId;
	}
}