<?php

namespace LaszloKorte\Resource;

use LaszloKorte\Graph\Graph;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class EntityConverter {

	private $graph;

	public function __construct(Graph $graph) {
		$this->graph = $graph;
	}

	public function convert($entityName) {
		if(!$this->graph->hasEntity($entityName)) {
			throw new NotFoundHttpException();
		}
		return $this->graph->entity($entityName);
	}
}