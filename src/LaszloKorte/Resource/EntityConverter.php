<?php

namespace LaszloKorte\Resource;

use LaszloKorte\Graph\Graph;

final class EntityConverter {

	private $application;

	public function __construct(Graph $application) {
		$this->application = $application;
	}

	public function convert($entityName) {
		return $this->application->entity($entityName);
	}
}