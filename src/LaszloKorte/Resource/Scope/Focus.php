<?php

namespace LaszloKorte\Resource\Scope;

final class Focus {
	private $entityId;
	private $recordId;

	public function __construct($entityId, $recordId) {
		$this->entityId = $entityId;
		$this->recordId = $recordId;
	}
}